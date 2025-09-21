/* Worker: parsing Excel + streaming batch, dengan logging detail.
   SheetJS URL diberikan dari main thread lewat message.
*/

function log(msg) {
    try { self.postMessage({ type: 'log', msg }); } catch (_) {}
}

// Kabari main thread bahwa file worker sudah ter-load
log('ready: worker file loaded');

function toNumber(val){
    if (val == null) return 0;
    if (typeof val === 'number') return val;
    if (typeof val === 'string') {
        const n = parseFloat(val.replace(/\./g,'').replace(/,/g,'.'));
        return isNaN(n) ? 0 : n;
    }
    return 0;
}
function excelDateToISO(v){
    if (typeof v==='number'){
        const d = new Date((Math.floor(v-25569)*86400)*1000);
        return d.toISOString().split('T')[0];
    }
    if (typeof v==='string' && v.trim()!==''){
        const d = new Date(v); if(!isNaN(d)) return d.toISOString().split('T')[0];
        const m = v.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})$/);
        if (m){ const dd = new Date(+m[3], +m[2]-1, +m[1]); if(!isNaN(dd)) return dd.toISOString().split('T')[0]; }
    }
    return '';
}
function mapRow(r){
    return {
        purc_doc: r['Pur. Doc.'] ?? '',
        sales_doc: r['Sales Doc'] ?? '',
        item: r['Item'] ?? '',
        material: String(r['Material'] ?? '').replace(/\./g,''),
        po_item_desc: r['PO Item Desc'] ?? '',
        prod_hierarchy_desc: r['Prod Hierarchy Desc'] ?? '',
        acc_ass_cat: r['Acc Ass Cat'] ?? '',
        vendor_name: r['Vendor name'] ?? '',
        customer_name: r['Customer name'] ?? '',
        stor_loc: r['Stor Loc'] ?? '',
        sloc_desc: r['SLoc Desc'] ?? '',
        valuation: r['Valuation'] ?? '',
        po_item_qty: parseInt(toNumber(r['PO Itm Qty'])) || 0,
        net_order_price: toNumber(r['Net Price']),
        currency: r['Crcy'] ?? '',
        date: excelDateToISO(r['Created On'])
    };
}

self.onmessage = (e) => {
    const { cmd } = e.data || {};
    if (cmd !== 'parse') return;

    const { sheetUrl, arrayBuffer, batchSize = 1000 } = e.data;

    try {
        log('beforeImport: ' + sheetUrl);
        importScripts(sheetUrl);             // <- jika ini gagal, kita kirim error di catch
        log('afterImport: XLSX is ' + (typeof XLSX));

        const data = new Uint8Array(arrayBuffer);
        const wb = XLSX.read(data, { type: 'array' });
        const ws = wb.Sheets[wb.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });

        const total = rows.length;
        self.postMessage({ type: 'meta', total });

        let sent = 0, buf = [];
        for (let i = 0; i < rows.length; i++) {
            buf.push(mapRow(rows[i]));
            if (buf.length >= batchSize) {
                sent += buf.length;
                self.postMessage({ type: 'batch', rows: buf });
                self.postMessage({ type: 'progress', sent, total });
                buf = [];
            }
        }
        if (buf.length) {
            sent += buf.length;
            self.postMessage({ type: 'batch', rows: buf });
            self.postMessage({ type: 'progress', sent, total });
        }
        self.postMessage({ type: 'done' });
    } catch (err) {
        self.postMessage({
            type: 'error',
            message: (err && err.message) ? err.message : String(err),
            stack: err && err.stack ? String(err.stack) : null
        });
    }
};
