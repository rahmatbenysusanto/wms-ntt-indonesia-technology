@extends('layout.index')
@section('title', 'Purchase Order')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Upload PO</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Inbound</a></li>
                        <li class="breadcrumb-item">Purchase Order</li>
                        <li class="breadcrumb-item active">Upload</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Dropzone</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload file excel hasil dari SAP</p>
                    <div class="mb-3">
                        <input type="file" class="form-control" id="excelFile">
                    </div>
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-info" id="uploadBtn">Upload File</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">List Data Upload</h4>
                        <a class="btn btn-primary" onclick="processImport()">Import Purchase Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purc Doc</th>
                                    <th>Sales Doc</th>
                                    <th>Item</th>
                                    <th>Material</th>
                                    <th>PO Item Desc</th>
                                    <th>Prod Hierarchy Desc</th>
                                    <th>Acc Ass Cat</th>
                                    <th>Vendor name</th>
                                    <th>Customer name</th>
                                    <th>Stor Loc</th>
                                    <th>SLoc Desc</th>
                                    <th>Valuation</th>
                                    <th>PO Itm Qty</th>
                                    <th>Net Order Price</th>
                                    <th>Currency</th>
                                </tr>
                            </thead>
                            <tbody id="dataUploadFile">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Pastikan Swal ada --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    {{-- Penting: JANGAN pakai defer, biar XLSX siap sebelum dipakai --}}
    <script src="{{ asset('assets/js/xlsx.full.min.js') }}"></script>

    <script>
        /* ================= Storage helper (Memory instead of sessionStorage) ================= */
        // SessionStorage memiliki limit (5-10MB). Untuk file besar, gunakan variabel global.
        window.GLOBAL_UPLOAD_DATA = [];

        const DATA_STORE = {
            set(val) {
                window.GLOBAL_UPLOAD_DATA = val;
            },
            get() {
                return window.GLOBAL_UPLOAD_DATA;
            },
            clear() {
                window.GLOBAL_UPLOAD_DATA = [];
            }
        };
        DATA_STORE.clear(); // clear di awal

        /* ================= Modal helpers ================= */
        function showBusyModal(msg = 'Membaca & menyiapkan file…') {
            if (window.Swal && Swal.isVisible()) {
                // kalau sudah ada swal terbuka, update saja
                Swal.update({
                    title: msg,
                    html: `
                        <div class="d-flex align-items-center gap-2">
                          <div class="spinner-border" role="status" aria-hidden="true"></div>
                          <span class="small text-muted">Mohon tunggu…</span>
                        </div>
                    `,
                    showConfirmButton: false
                });
                return;
            }
            Swal.fire({
                title: msg,
                html: `
                    <div class="d-flex align-items-center gap-2">
                      <div class="spinner-border" role="status" aria-hidden="true"></div>
                      <span class="small text-muted">Mohon tunggu…</span>
                    </div>
                `,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });
        }

        function showProgressModal(title) {
            if (window.Swal && Swal.isVisible()) {
                Swal.update({
                    title,
                    html: `
                        <div id="parseText" class="text-start small mb-2">Starting…</div>
                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height:12px">
                          <div id="parseBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                        </div>
                    `,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title,
                    html: `
                        <div id="parseText" class="text-start small mb-2">Starting…</div>
                        <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height:12px">
                          <div id="parseBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading(),
                });
            }
        }

        function upgradeToProgressModal(title, total) {
            showProgressModal(title); // pastikan struktur bar ada
            const txt = document.getElementById('parseText');
            if (txt) txt.textContent = `Processed 0/${total} rows (0%)`;
        }

        function updateProgressModal(done, total) {
            const pct = total ? Math.floor((done / total) * 100) : 0;
            const bar = document.getElementById('parseBar');
            const txt = document.getElementById('parseText');
            if (bar) {
                bar.style.width = pct + '%';
                bar.setAttribute('aria-valuenow', pct);
                bar.textContent = pct + '%';
            }
            if (txt) txt.textContent = `Processed ${done}/${total} rows (${pct}%)`;
        }

        function closeProgressModal() {
            if (window.Swal && Swal.isVisible()) Swal.close();
        }
        const nextFrame = () => new Promise(r => requestAnimationFrame(r)); // repaint-friendly

        /* ================= Konfigurasi preview (0 = tampilkan semua) ================= */
        const PREVIEW_LIMIT = 0;

        /* ================= Table render ================= */
        function renderTable(rows) {
            const tbody = document.getElementById("dataUploadFile");
            const frag = document.createDocumentFragment();
            let n = 1;
            const limit = PREVIEW_LIMIT ? Math.min(PREVIEW_LIMIT, rows.length) : rows.length;
            for (let i = 0; i < limit; i++) {
                const r = rows[i];
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${n++}</td>
                    <td>${r.purc_doc ?? ''}</td>
                    <td>${r.sales_doc ?? ''}</td>
                    <td>${r.item ?? ''}</td>
                    <td>${r.material ?? ''}</td>
                    <td>${r.po_item_desc ?? ''}</td>
                    <td>${r.prod_hierarchy_desc ?? ''}</td>
                    <td>${r.acc_ass_cat ?? ''}</td>
                    <td>${r.vendor_name ?? ''}</td>
                    <td>${r.customer_name ?? ''}</td>
                    <td>${r.stor_loc ?? ''}</td>
                    <td>${r.sloc_desc ?? ''}</td>
                    <td>${r.valuation ?? ''}</td>
                    <td>${r.po_item_qty ?? ''}</td>
                    <td>${r.net_order_price ?? ''}</td>
                    <td>${r.currency ?? ''}</td>`;
                frag.appendChild(tr);
            }
            tbody.innerHTML = '';
            tbody.appendChild(frag);
        }

        /* ================= Utils ================= */
        function toNumber(val) {
            if (val == null) return 0;
            if (typeof val === 'number') return val;
            if (typeof val === 'string') {
                const n = parseFloat(val.replace(/\./g, '').replace(/,/g, '.'));
                return isNaN(n) ? 0 : n;
            }
            return 0;
        }

        function excelDateToISO(v) {
            if (typeof v === 'number') {
                const d = new Date((Math.floor(v - 25569) * 86400) * 1000);
                return d.toISOString().split('T')[0];
            }
            if (typeof v === 'string' && v.trim() !== '') {
                const d = new Date(v);
                if (!isNaN(d)) return d.toISOString().split('T')[0];
                const m = v.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
                if (m) {
                    const dd = new Date(+m[3], +m[2] - 1, +m[1]);
                    if (!isNaN(dd)) return dd.toISOString().split('T')[0];
                }
            }
            return '';
        }

        function mapRowByIndex(row, idx) {
            const materialRaw = row[idx['Material']];
            return {
                purc_doc: row[idx['Pur. Doc.']],
                sales_doc: row[idx['Sales Doc']],
                item: row[idx['Item']],
                material: String(materialRaw ?? '').replace(/\./g, ''),
                po_item_desc: row[idx['PO Item Desc']],
                prod_hierarchy_desc: row[idx['Prod Hierarchy Desc']],
                acc_ass_cat: row[idx['Acc Ass Cat']],
                vendor_name: row[idx['Vendor name']],
                customer_name: row[idx['Customer name']],
                stor_loc: row[idx['Stor Loc']],
                sloc_desc: row[idx['SLoc Desc']],
                valuation: row[idx['Valuation']],
                po_item_qty: parseInt(toNumber(row[idx['PO Itm Qty']])) || 0,
                net_order_price: toNumber(row[idx['Net Price']]),
                currency: row[idx['Crcy']],
                date: excelDateToISO(row[idx['Created On']])
            };
        }

        /* ================= Upload / Parse flow (2 tahap modal) ================= */
        DATA_STORE.clear();

        document.getElementById('uploadBtn').addEventListener('click', async function(evt) {
            evt.preventDefault(); // kalau tombolnya <a>, cegah navigasi
            const fileInput = document.getElementById('excelFile');
            const file = fileInput.files[0];
            if (!file) {
                Swal.fire('File belum dipilih', 'Silakan pilih file Excel terlebih dahulu.', 'warning');
                return;
            }

            // Tahap 1: Tampilkan spinner, lalu beri waktu browser untuk render
            showBusyModal('Membaca & menyiapkan file…');
            await nextFrame(); // 1 frame
            await nextFrame(); // +1 frame (beberapa browser butuh 2 frame utk render modal)

            // Mulai baca file (asinkron, tidak menghambat render spinner)
            const reader = new FileReader();
            reader.onerror = (e) => {
                console.error('FileReader error', e);
                closeProgressModal();
                Swal.fire('Gagal', 'Tidak bisa membaca file. Coba ulangi.', 'error');
            };
            reader.onload = async (e) => {
                try {
                    // beri napas 1 frame biar spinner terlihat
                    await nextFrame();

                    // Parse workbook
                    const wb = XLSX.read(new Uint8Array(e.target.result), {
                        type: 'array'
                    });
                    const ws = wb.Sheets[wb.SheetNames[0]];

                    // === 1) Baca sebagai JSON baris-objek (stabil), JANGAN 2D-array
                    const json = XLSX.utils.sheet_to_json(ws, {
                        defval: "", // kosong jadi string kosong (bukan undefined)
                        raw: true, // angka/tanggal tetap mentah, kita olah sendiri
                        blankrows: false
                    });

                    // === 2) Helper: normalisasi & ambil kolom (toleran variasi header)
                    const norm = s => String(s || "").trim().replace(/\s+/g, ' ').replace(/\.$/, '')
                        .toLowerCase();
                    const aliases = {
                        "pur. doc.": ["pur. doc.", "pur. doc", "pur doc", "purchase doc",
                            "purch doc"
                        ],
                        "sales doc": ["sales doc", "sales document"],
                        "item": ["item", "itm"],
                        "material": ["material", "material no", "material number"],
                        "po item desc": ["po item desc", "po item description", "description"],
                        "prod hierarchy desc": ["prod hierarchy desc", "product hierarchy desc"],
                        "acc ass cat": ["acc ass cat", "account assignment cat",
                            "account assignment"
                        ],
                        "vendor name": ["vendor name", "vendor", "supp name", "supplier name"],
                        "customer name": ["customer name", "customer", "sold-to name"],
                        "stor loc": ["stor loc", "storage loc", "storage location", "sloc"],
                        "sloc desc": ["sloc desc", "storage location desc"],
                        "valuation": ["valuation"],
                        "po itm qty": ["po itm qty", "po item qty", "quantity", "qty"],
                        "net price": ["net price", "net order price", "price"],
                        "crcy": ["crcy", "currency"],
                        "created on": ["created on", "creation date", "date"]
                    };
                    // buat peta dari header normalisasi -> header asli yang ada di file
                    const headerSet = new Set(Object.keys(json[0] || {}).map(norm));
                    const pickHeader = (key) => {
                        const list = aliases[key] || [key];
                        for (const cand of list) {
                            // coba cocokkan langsung
                            for (const real in (json[0] || {})) {
                                if (norm(real) === norm(cand))
                                    return real; // kembalikan nama kolom asli
                            }
                        }
                        return null;
                    };

                    const H = {
                        purc_doc: pickHeader("pur. doc."),
                        sales_doc: pickHeader("sales doc"),
                        item: pickHeader("item"),
                        material: pickHeader("material"),
                        po_item_desc: pickHeader("po item desc"),
                        prod_hier: pickHeader("prod hierarchy desc"),
                        acc_ass_cat: pickHeader("acc ass cat"),
                        vendor_name: pickHeader("vendor name"),
                        customer_name: pickHeader("customer name"),
                        stor_loc: pickHeader("stor loc"),
                        sloc_desc: pickHeader("sloc desc"),
                        valuation: pickHeader("valuation"),
                        po_itm_qty: pickHeader("po itm qty"),
                        net_price: pickHeader("net price"),
                        crcy: pickHeader("crcy"),
                        created_on: pickHeader("created on")
                    };

                    // === 3) Baris valid = minimal ada isi di salah satu kolom KUNCI
                    const hasAny = (row, keys) => keys.some(k => {
                        const h = H[k];
                        if (!h) return false;
                        return String(row[h] ?? "").trim() !== "";
                    });

                    const dataRows = json.filter(r =>
                        hasAny(r, ["purc_doc", "sales_doc", "item", "material", "po_item_desc"])
                    );

                    const total = dataRows.length;

                    // === 4) Ubah modal menjadi progress bar
                    upgradeToProgressModal('Parsing Excel', total);

                    // === 5) Map + progress (chunked)
                    const poCache = [];
                    const CHUNK = 1000;
                    const STEP_UPDATE = 200;
                    let done = 0;

                    for (let i = 0; i < dataRows.length; i++) {
                        const r = dataRows[i];
                        const get = (h) => (h && r[h] != null) ? r[h] : "";

                        poCache.push({
                            purc_doc: get(H.purc_doc),
                            sales_doc: get(H.sales_doc),
                            item: get(H.item),
                            material: String(get(H.material)).replace(/\./g, ""),
                            po_item_desc: get(H.po_item_desc),
                            prod_hierarchy_desc: get(H.prod_hier),
                            acc_ass_cat: get(H.acc_ass_cat),
                            vendor_name: get(H.vendor_name),
                            customer_name: get(H.customer_name),
                            stor_loc: get(H.stor_loc),
                            sloc_desc: get(H.sloc_desc),
                            valuation: get(H.valuation),
                            po_item_qty: parseInt((() => {
                                const v = get(H.po_itm_qty);
                                if (typeof v === "number") return v;
                                return toNumber(v);
                            })()) || 0,
                            net_order_price: (() => {
                                const v = get(H.net_price);
                                if (typeof v === "number") return v;
                                return toNumber(v);
                            })(),
                            currency: get(H.crcy),
                            date: (() => {
                                const v = get(H.created_on);
                                return excelDateToISO(v);
                            })()
                        });

                        done++;
                        if (done % STEP_UPDATE === 0) updateProgressModal(done, total);
                        if (done % CHUNK === 0) await nextFrame();
                    }
                    updateProgressModal(total, total);

                    // === 6) Simpan SEKALI ke Memory + render
                    DATA_STORE.set(poCache);
                    renderTable(poCache);
                    closeProgressModal();

                } catch (err) {
                    console.error(err);
                    closeProgressModal();
                    Swal.fire('Gagal', 'Parsing gagal. Pastikan format Excel benar.', 'error');
                }
            };

            // Mulai benar-benar membaca file SETELAH spinner tampil & dirender
            reader.readAsArrayBuffer(file);
        });

        /* ================= Import: kirim per-batch dari sessionStorage ================= */
        async function sendBatchAjax(batch) {
            const res = await fetch(`{{ route('inbound.purchase-order-upload-process') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    purchaseOrder: batch
                })
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        }

        window.processImport = function() {
            Swal.fire({
                title: "Are you sure?",
                text: "Import Purchase Order",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-primary w-xs me-2 mt-2",
                    cancelButton: "btn btn-danger w-xs mt-2"
                },
                confirmButtonText: "Yes, Import it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(async (t) => {
                if (!t.value) return;

                const allData = DATA_STORE.get() || [];
                if (!allData.length) {
                    Swal.fire('Tidak ada data', 'Silakan upload & parse file terlebih dahulu.', 'info');
                    return;
                }

                const total = allData.length;
                const batchSize = 250; // Kurangi dari 1000 ke 250 agar server tidak timeout
                let sent = 0;

                showProgressModal('Importing to Server');
                updateProgressModal(0, total);

                try {
                    for (let i = 0; i < total; i += batchSize) {
                        const batch = allData.slice(i, i + batchSize);
                        await sendBatchAjax(batch);
                        sent += batch.length;
                        updateProgressModal(sent, total);
                        await nextFrame();
                    }
                    closeProgressModal();
                    Swal.fire({
                        title: 'Success!',
                        text: 'Import Purchase Order successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: "btn btn-primary w-xs mt-2"
                        },
                        buttonsStyling: false
                    }).then(() => {
                        DATA_STORE.clear();
                        window.location.href = '{{ route('inbound.purchase-order') }}';
                    });
                } catch (err) {
                    console.error(err);
                    closeProgressModal();
                    Swal.fire('Error', 'Import gagal pada salah satu batch.', 'error');
                }
            });
        };
    </script>
@endsection
