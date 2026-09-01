/* ============================================================================
 * excel-trim.js
 *
 * Membuang baris kosong (<row .../>) dari XML worksheet Excel SEBELUM parsing
 * SheetJS. Menyelesaikan kasus file export SAP yang mengklaim 1 juta baris
 * (dimensi A1:EO1048576) padahal data aslinya hanya puluhan baris — tanpa
 * pembersihan ini, XLSX.read + sheet_to_json bisa membekukan browser berjam-jam.
 *
 * Alur: baca central directory zip (byte math, tanpa decompress) → inflate tiap
 * entry (DecompressionStream, method 8) → buang <row .../> self-closing dan
 * <dimension> dari worksheet XML → rebuild zip dengan entry STORED (tanpa
 * kompresi, tidak butuh library deflate) → XLSX.read jadi instan.
 *
 * API:
 *   ExcelTrim.prepare(arrayBuffer, maxRows)
 *     → Promise<{ buffer: ArrayBuffer, declaredRows: number,
 *                 firstDataRowBeyondCap: number } | null>
 *       - buffer: zip yang sudah dibersihkan (atau file asli bila tidak ada
 *         baris kosong yang perlu dibuang)
 *       - declaredRows: jumlah baris yang diklaim <dimension>
 *       - firstDataRowBeyondCap: baris data nyata (punya <c>) pertama yang
 *         melebihi maxRows (0 = tidak ada)
 *       - null: tidak bisa diproses (.xls legacy, method zip tak didukung,
 *         DecompressionStream tidak tersedia, dsb.) → panggil XLSX.read
 *         langsung pada file asli dengan opsi sheetRows
 * ========================================================================== */
(function (global) {
    'use strict';

    var CRC_TABLE = (function () {
        var t = new Int32Array(256);
        for (var n = 0; n < 256; n++) {
            var c = n;
            for (var k = 0; k < 8; k++) c = c & 1 ? 0xEDB88320 ^ (c >>> 1) : c >>> 1;
            t[n] = c;
        }
        return t;
    })();

    function crc32(buf) {
        var c = -1;
        for (var i = 0; i < buf.length; i++) c = CRC_TABLE[(c ^ buf[i]) & 0xFF] ^ (c >>> 8);
        return (c ^ -1) >>> 0;
    }

    function concat(arrays) {
        var total = 0;
        for (var i = 0; i < arrays.length; i++) total += arrays[i].length;
        var out = new Uint8Array(total);
        var o = 0;
        for (var j = 0; j < arrays.length; j++) {
            out.set(arrays[j], o);
            o += arrays[j].length;
        }
        return out;
    }

    /** Inflate raw deflate (zip method 8) via API browser native. null bila tak didukung. */
    function inflateRaw(bytes) {
        if (typeof DecompressionStream === 'undefined') return null;
        var stream = new Blob([bytes]).stream().pipeThrough(new DecompressionStream('deflate-raw'));
        return new Response(stream).arrayBuffer().then(function (ab) {
            return new Uint8Array(ab);
        });
    }

    /**
     * Baca daftar entry dari central directory zip.
     * Kembali null bila bukan zip (mis. .xls BIFF).
     */
    function readZipEntries(arrayBuffer) {
        var dv = new DataView(arrayBuffer);
        if (dv.byteLength < 4 || dv.getUint32(0, true) !== 0x04034b50) return null;

        var eocd = -1;
        for (var i = arrayBuffer.byteLength - 22; i >= 0; i--) {
            if (dv.getUint32(i, true) === 0x06054b50) { eocd = i; break; }
        }
        if (eocd < 0) return null;

        var cdOffset = dv.getUint32(eocd + 16, true);
        var cdCount = dv.getUint16(eocd + 10, true);
        var entries = [];
        for (var p = cdOffset, n = 0; n < cdCount; n++) {
            if (dv.getUint32(p, true) !== 0x02014b50) break;
            var method = dv.getUint16(p + 10, true);
            var csize = dv.getUint32(p + 20, true);
            var nlen = dv.getUint16(p + 28, true);
            var elen = dv.getUint16(p + 30, true);
            var clen = dv.getUint16(p + 32, true);
            var lho = dv.getUint32(p + 42, true);
            var name = '';
            try {
                name = new TextDecoder().decode(new Uint8Array(arrayBuffer, p + 46, nlen));
            } catch (ignored) { /* nama tak terbaca — biarkan kosong */ }
            entries.push({ name: name, method: method, csize: csize, lho: lho });
            p += 46 + nlen + elen + clen;
        }
        return entries.length ? entries : null;
    }

    /** Ambil isi satu entry; otomatis di-inflate bila method 8. null = tak didukung. */
    function readEntryBytes(arrayBuffer, entry) {
        var dv = new DataView(arrayBuffer);
        var nlen = dv.getUint16(entry.lho + 26, true);
        var elen = dv.getUint16(entry.lho + 28, true);
        var start = entry.lho + 30 + nlen + elen;
        var raw = new Uint8Array(arrayBuffer, start, entry.csize);
        if (entry.method === 0) return Promise.resolve(raw.slice());
        if (entry.method === 8) return inflateRaw(raw);
        return Promise.resolve(null); // method lain (12 bzip2, dsb.) — fallback
    }

    /** Bangun ulang zip: semua entry disimpan STORED (tanpa kompresi). */
    function buildStoredZip(contents) {
        var locals = [];
        var cds = [];
        var offset = 0;
        var enc = new TextEncoder();

        for (var i = 0; i < contents.length; i++) {
            var name = contents[i].name;
            var bytes = contents[i].bytes;
            var nb = enc.encode(name);
            var crc = crc32(bytes);

            var lh = new Uint8Array(30);
            var lv = new DataView(lh.buffer);
            lv.setUint32(0, 0x04034b50, true);   // PK\x03\x04
            lv.setUint16(4, 20, true);           // version needed
            lv.setUint16(8, 0, true);            // method = stored
            lv.setUint32(14, crc, true);
            lv.setUint32(18, bytes.length, true);
            lv.setUint32(22, bytes.length, true);
            lv.setUint16(26, nb.length, true);

            var cd = new Uint8Array(46);
            var cv = new DataView(cd.buffer);
            cv.setUint32(0, 0x02014b50, true);   // PK\x01\x02
            cv.setUint16(4, 20, true);
            cv.setUint16(6, 20, true);
            cv.setUint16(10, 0, true);           // method = stored
            cv.setUint32(16, crc, true);
            cv.setUint32(20, bytes.length, true);
            cv.setUint32(24, bytes.length, true);
            cv.setUint16(28, nb.length, true);
            cv.setUint32(42, offset, true);

            locals.push(concat([lh, nb, bytes]));
            cds.push(concat([cd, nb]));
            offset += lh.length + nb.length + bytes.length;
        }

        var cdBytes = concat(cds);
        var eocd = new Uint8Array(22);
        var ev = new DataView(eocd.buffer);
        ev.setUint32(0, 0x06054b50, true);       // PK\x05\x06
        ev.setUint16(8, contents.length, true);
        ev.setUint16(10, contents.length, true);
        ev.setUint32(12, cdBytes.length, true);
        ev.setUint32(16, offset, true);

        return concat([concat(locals), cdBytes, eocd]).buffer;
    }

    /**
     * Siapkan file untuk parsing (lihat deskripsi di header).
     * maxRows: batas baris yang dianggap aman (untuk deteksi "file terlalu besar").
     */
    function prepare(arrayBuffer, maxRows) {
        maxRows = maxRows || 10000;

        try {
            var entries = readZipEntries(arrayBuffer);
            if (!entries) return Promise.resolve(null);

            var contents = [];
            var declaredRows = 0;
            var firstBeyond = 0;
            var anyStripped = false;
            var work = [];

            for (var i = 0; i < entries.length; i++) {
                (function (entry) {
                    work.push(readEntryBytes(arrayBuffer, entry).then(function (bytes) {
                        if (!bytes) return null; // entry tak didukung → batal total

                        if (/^xl\/worksheets\/sheet\d+\.xml$/.test(entry.name)) {
                            var xml;
                            try {
                                xml = new TextDecoder().decode(bytes);
                            } catch (e) {
                                return null;
                            }

                            var dm = xml.match(/<dimension[^>]*ref="[A-Z]+\d+:([A-Z]+)(\d+)"/);
                            if (dm) declaredRows = Math.max(declaredRows, parseInt(dm[2], 10));

                            // baris data nyata (berisi <c ...>) pertama yang melewati batas
                            var re = /<row[^>]*r="(\d+)"[^>]*><c /g;
                            var m;
                            while ((m = re.exec(xml)) !== null) {
                                var r = parseInt(m[1], 10);
                                if (r > maxRows) { firstBeyond = r; break; }
                            }

                            // buang baris kosong (self-closing) + dimension (biar SheetJS hitung ulang !ref)
                            var cleaned = xml
                                .replace(/<row[^>]*\/>/g, '')
                                .replace(/<dimension[^>]*\/>/g, '');
                            if (cleaned !== xml) anyStripped = true;

                            contents.push({ name: entry.name, bytes: new TextEncoder().encode(cleaned) });
                        } else {
                            contents.push({ name: entry.name, bytes: bytes });
                        }
                        return true;
                    }));
                })(entries[i]);
            }

            return Promise.all(work).then(function (results) {
                for (var r = 0; r < results.length; r++) {
                    if (results[r] === null) return null; // ada entry yang gagal → fallback
                }
                return {
                    buffer: anyStripped ? buildStoredZip(contents) : arrayBuffer,
                    declaredRows: declaredRows,
                    firstDataRowBeyondCap: firstBeyond
                };
            });
        } catch (err) {
            console.warn('ExcelTrim.prepare dilewati:', err);
            return Promise.resolve(null);
        }
    }

    global.ExcelTrim = { prepare: prepare };
})(typeof window !== 'undefined' ? window : globalThis);
