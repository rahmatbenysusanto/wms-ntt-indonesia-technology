<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    /**
     * Predefined intents mapped to safe, parameterized queries.
     * Each intent has a 'query' callable that receives params and returns array data.
     */
    private const INTENTS = [
        'stock_check',
        'aging_check',
        'serial_number_lookup',
        'box_status',
        'po_status',
        'outbound_summary',
        'inventory_summary',
        'product_history',
        'general_chat',
    ];

    public function __construct()
    {
        $this->apiKey  = config('services.deepseek.api_key');
        $this->baseUrl = config('services.deepseek.base_url', 'https://api.deepseek.com');
        $this->model   = config('services.deepseek.model', 'deepseek-v4-flash');
    }

    /**
     * Process a user message and return the assistant's reply.
     */
    public function chat(string $userMessage, ChatConversation $conversation): string
    {
        // Step 1: Save user message
        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'role'                 => 'user',
            'content'              => $userMessage,
            'created_at'           => now(),
        ]);

        // Auto-title the conversation from the first user message
        if (empty($conversation->title)) {
            $conversation->title = mb_strlen($userMessage) > 50
                ? mb_substr($userMessage, 0, 47) . '...'
                : $userMessage;
            $conversation->save();
        }

        // Step 2: Classify intent + extract params via DeepSeek
        $classification = $this->classifyIntent($userMessage, $conversation);
        $intent         = $classification['intent'] ?? 'general_chat';
        $params         = $classification['params'] ?? [];

        Log::info('AI Chat intent classified', [
            'intent' => $intent,
            'params' => $params,
        ]);

        // Step 3: Execute query if it's a data intent
        $queryResult = null;
        if ($intent !== 'general_chat') {
            $queryResult = $this->executeIntentQuery($intent, $params);
        }

        // Step 4: Generate natural language reply
        $reply = $this->generateReply($userMessage, $intent, $params, $queryResult, $conversation);

        // Step 5: Save assistant message
        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'role'                 => 'assistant',
            'content'              => $reply,
            'metadata'             => [
                'intent' => $intent,
                'params' => $params,
            ],
            'created_at'           => now(),
        ]);

        return $reply;
    }

    /**
     * Call DeepSeek to classify the user's intent and extract parameters.
     */
    private function classifyIntent(string $userMessage, ChatConversation $conversation): array
    {
        $systemPrompt = $this->getIntentClassificationPrompt();

        // Get recent conversation for context
        $recentMessages = $this->getRecentContext($conversation);

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $recentMessages,
            [['role' => 'user', 'content' => $userMessage]]
        );

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => $messages,
                    'temperature' => 0.1,
                    'max_tokens'  => 300,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                return $this->parseJsonResponse($content);
            }

            Log::error('DeepSeek intent classification failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('DeepSeek API error: ' . $e->getMessage());
        }

        return ['intent' => 'general_chat', 'params' => []];
    }

    /**
     * Generate the final natural language reply.
     */
    private function generateReply(
        string $userMessage,
        string $intent,
        array $params,
        ?array $queryResult,
        ChatConversation $conversation
    ): string {
        // For general chat or when query has no results, use the classification's pre-generated reply
        if ($intent === 'general_chat') {
            return $this->generateGeneralChatReply($userMessage, $conversation);
        }

        if (empty($queryResult) || (is_array($queryResult) && count($queryResult) === 0)) {
            return $this->formatEmptyResult($intent, $params);
        }

        return $this->formatDataReply($userMessage, $intent, $params, $queryResult, $conversation);
    }

    /**
     * Execute a predefined, safe query based on intent and params.
     */
    private function executeIntentQuery(string $intent, array $params): ?array
    {
        return match ($intent) {
            'stock_check'          => $this->queryStockCheck($params),
            'aging_check'          => $this->queryAgingCheck($params),
            'serial_number_lookup' => $this->querySerialNumber($params),
            'box_status'           => $this->queryBoxStatus($params),
            'po_status'            => $this->queryPoStatus($params),
            'outbound_summary'     => $this->queryOutboundSummary($params),
            'inventory_summary'    => $this->queryInventorySummary($params),
            'product_history'      => $this->queryProductHistory($params),
            default                => null,
        };
    }

    // ========================================================================
    // Predefined Safe Queries
    // ========================================================================

    private function queryStockCheck(array $params): array
    {
        $material = $params['material'] ?? '';
        $product  = $params['product_name'] ?? '';

        $query = DB::table('product')
            ->join('inventory_item', 'product.id', '=', 'inventory_item.product_id')
            ->join('inventory', 'inventory_item.purc_doc', '=', 'inventory.purchase_order_id')
            ->leftJoin('storage', 'inventory_item.storage_id', '=', 'storage.id')
            ->select(
                'product.material',
                'product.po_item_desc',
                'inventory_item.stock',
                'inventory_item.type',
                'inventory_item.sales_doc',
                'storage.area',
                'storage.rak',
                'storage.bin'
            )
            ->where('inventory_item.stock', '>', 0);

        if ($material) {
            $query->where('product.material', 'like', "%{$material}%");
        }
        if ($product) {
            $query->where('product.po_item_desc', 'like', "%{$product}%");
        }

        return $query->limit(50)->get()->toArray();
    }

    private function queryAgingCheck(array $params): array
    {
        $days         = intval($params['days'] ?? 90);
        $customerName = $params['customer_name'] ?? '';
        $cutoffDate   = now()->subDays($days)->format('Y-m-d');

        $query = DB::table('inventory_detail')
            ->join('inventory', 'inventory_detail.inventory_id', '=', 'inventory.id')
            ->join('inventory_package_item', 'inventory_detail.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->join('inventory_package', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->join('product', 'inventory_package_item.product_id', '=', 'product.id')
            ->join('purchase_order_detail', 'inventory_detail.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->select(
                'inventory_package.number as box_number',
                'product.material',
                'product.po_item_desc',
                'inventory_detail.qty',
                'inventory_detail.aging_date',
                'purchase_order_detail.customer_name',
                DB::raw('DATEDIFF(NOW(), inventory_detail.aging_date) as age_days')
            )
            ->where('inventory_detail.qty', '>', 0)
            ->whereDate('inventory_detail.aging_date', '<=', $cutoffDate);

        if ($customerName) {
            $query->where('purchase_order_detail.customer_name', 'like', "%{$customerName}%");
        }

        return $query->orderBy('age_days', 'desc')
            ->limit(50)
            ->get()
            ->toArray();
    }

    private function querySerialNumber(array $params): array
    {
        $sn = $params['serial_number'] ?? '';

        if (empty($sn)) {
            return [];
        }

        // Search across all SN tables
        $results = [];

        // Check inventory_package_item_sn
        $invSn = DB::table('inventory_package_item_sn')
            ->join('inventory_package_item', 'inventory_package_item_sn.inventory_package_item_id', '=', 'inventory_package_item.id')
            ->join('inventory_package', 'inventory_package_item.inventory_package_id', '=', 'inventory_package.id')
            ->join('product', 'inventory_package_item.product_id', '=', 'product.id')
            ->leftJoin('storage', 'inventory_package.storage_id', '=', 'storage.id')
            ->where('inventory_package_item_sn.serial_number', $sn)
            ->select(
                'product.material',
                'product.po_item_desc',
                'inventory_package.number as box_number',
                'inventory_package_item_sn.serial_number',
                'storage.area',
                'storage.rak',
                'storage.bin',
                'inventory_package.created_at'
            )
            ->first();

        if ($invSn) {
            $results[] = $invSn;
        }

        // Check outbound_detail_sn
        $outSn = DB::table('outbound_detail_sn')
            ->join('outbound_detail', 'outbound_detail_sn.outbound_detail_id', '=', 'outbound_detail.id')
            ->join('outbound', 'outbound_detail.outbound_id', '=', 'outbound.id')
            ->where('outbound_detail_sn.serial_number', $sn)
            ->select(
                'outbound.number as outbound_number',
                'outbound_detail_sn.serial_number',
                'outbound.outbound_date',
                'outbound.deliv_dest'
            )
            ->first();

        if ($outSn) {
            $results[] = $outSn;
        }

        // Check SN change log
        $changes = DB::table('sn_change_log')
            ->where('old_serial_number', $sn)
            ->orWhere('new_serial_number', $sn)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return [
            'current'  => $results,
            'history'  => $changes,
            'searched' => $sn,
        ];
    }

    private function queryBoxStatus(array $params): array
    {
        $boxNumber = $params['box_number'] ?? '';

        if (empty($boxNumber)) {
            return [];
        }

        return DB::table('inventory_package')
            ->join('purchase_order', 'inventory_package.purchase_order_id', '=', 'purchase_order.id')
            ->leftJoin('storage', 'inventory_package.storage_id', '=', 'storage.id')
            ->where('inventory_package.number', 'like', "%{$boxNumber}%")
            ->select(
                'inventory_package.number',
                'inventory_package.qty_item',
                'inventory_package.qty',
                'inventory_package.reff_number',
                'inventory_package.note',
                'inventory_package.created_at',
                'purchase_order.purc_doc',
                'storage.area',
                'storage.rak',
                'storage.bin'
            )
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function queryPoStatus(array $params): array
    {
        $poNumber = $params['po_number'] ?? '';

        if (empty($poNumber)) {
            return [];
        }

        return DB::table('purchase_order')
            ->join('purchase_order_detail', 'purchase_order.id', '=', 'purchase_order_detail.purchase_order_id')
            ->join('product', 'purchase_order_detail.product_id', '=', 'product.id')
            ->leftJoin('inventory', 'purchase_order.id', '=', 'inventory.purchase_order_id')
            ->where('purchase_order.purc_doc', 'like', "%{$poNumber}%")
            ->select(
                'purchase_order.purc_doc',
                'purchase_order.status',
                'purchase_order.sales_doc_qty',
                'purchase_order.material_qty',
                'purchase_order.created_at',
                'purchase_order_detail.sales_doc',
                'purchase_order_detail.customer_name',
                'product.material',
                'product.po_item_desc',
                'purchase_order_detail.po_item_qty',
                'inventory.stock'
            )
            ->limit(30)
            ->get()
            ->toArray();
    }

    private function queryOutboundSummary(array $params): array
    {
        $customerName = $params['customer_name'] ?? '';
        $month        = $params['month'] ?? now()->month;
        $year         = $params['year'] ?? now()->year;

        // Summary by customer
        $summary = DB::table('outbound')
            ->join('customer', 'outbound.customer_id', '=', 'customer.id')
            ->whereMonth('outbound_date', $month)
            ->whereYear('outbound_date', $year);

        if ($customerName) {
            $summary->where('customer.name', 'like', "%{$customerName}%");
        }

        return $summary->select(
            'customer.name as customer_name',
            DB::raw('COUNT(*) as total_outbound'),
            DB::raw('SUM(outbound.qty_item) as total_items'),
            DB::raw('SUM(outbound.qty) as total_qty')
        )
            ->groupBy('customer.id', 'customer.name')
            ->orderByDesc('total_outbound')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function queryInventorySummary(array $params): array
    {
        $type = $params['type'] ?? 'inv';

        $totalStock = DB::table('inventory')
            ->where('type', $type)
            ->sum('stock');

        $totalBoxes = DB::table('inventory_package')
            ->join('inventory', 'inventory_package.purchase_order_id', '=', 'inventory.purchase_order_id')
            ->where('inventory.type', $type)
            ->where('inventory_package.qty', '>', 0)
            ->count();

        $byCustomer = DB::table('inventory_item')
            ->where('inventory_item.type', $type)
            ->where('inventory_item.stock', '>', 0)
            ->select(
                'inventory_item.sales_doc',
                DB::raw('SUM(inventory_item.stock) as total_stock')
            )
            ->groupBy('inventory_item.sales_doc')
            ->orderByDesc('total_stock')
            ->limit(10)
            ->get()
            ->toArray();

        $byStorage = DB::table('inventory_item')
            ->join('storage', 'inventory_item.storage_id', '=', 'storage.id')
            ->where('inventory_item.type', $type)
            ->where('inventory_item.stock', '>', 0)
            ->select(
                'storage.area',
                'storage.rak',
                DB::raw('SUM(inventory_item.stock) as total_stock')
            )
            ->groupBy('storage.area', 'storage.rak')
            ->orderByDesc('total_stock')
            ->limit(20)
            ->get()
            ->toArray();

        return [
            'type'          => $type,
            'total_stock'   => $totalStock,
            'total_boxes'   => $totalBoxes,
            'by_customer'   => $byCustomer,
            'by_storage'    => $byStorage,
        ];
    }

    private function queryProductHistory(array $params): array
    {
        $material = $params['material'] ?? '';

        if (empty($material)) {
            return [];
        }

        $history = DB::table('inventory_history')
            ->join('purchase_order_detail', 'inventory_history.purchase_order_detail_id', '=', 'purchase_order_detail.id')
            ->join('product', 'purchase_order_detail.product_id', '=', 'product.id')
            ->join('purchase_order', 'inventory_history.purchase_order_id', '=', 'purchase_order.id')
            ->where('product.material', 'like', "%{$material}%")
            ->select(
                'inventory_history.type',
                'inventory_history.qty',
                'inventory_history.note',
                'inventory_history.created_at',
                'purchase_order.purc_doc',
                'purchase_order_detail.sales_doc',
                'product.material',
                'product.po_item_desc'
            )
            ->orderBy('inventory_history.created_at', 'desc')
            ->limit(50)
            ->get()
            ->toArray();

        return $history;
    }

    // ========================================================================
    // Reply Formatting via DeepSeek
    // ========================================================================

    private function generateGeneralChatReply(string $userMessage, ChatConversation $conversation): string
    {
        $systemPrompt = $this->getGeneralChatPrompt();

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                    'max_tokens'  => 500,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content')
                    ?? 'Maaf, saya tidak bisa menjawab pertanyaan itu saat ini.';
            }

        } catch (\Exception $e) {
            Log::error('DeepSeek general chat error: ' . $e->getMessage());
        }

        return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi nanti.';
    }

    private function formatDataReply(
        string $userMessage,
        string $intent,
        array $params,
        array $data,
        ChatConversation $conversation
    ): string {
        $systemPrompt = $this->getDataFormattingPrompt($intent);

        $dataContext = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Truncate if too long to stay within token limits
        if (mb_strlen($dataContext) > 3000) {
            $dataContext = mb_substr($dataContext, 0, 3000) . "\n... (data terpotong)";
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/v1/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => "Pertanyaan user: \"{$userMessage}\"\n\nData hasil query:\n{$dataContext}\n\nTolong jawab pertanyaan user berdasarkan data di atas dalam Bahasa Indonesia yang natural dan mudah dipahami."],
                    ],
                    'temperature' => 0.5,
                    'max_tokens'  => 600,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content')
                    ?? 'Maaf, saya tidak bisa memproses data ini.';
            }

        } catch (\Exception $e) {
            Log::error('DeepSeek data formatting error: ' . $e->getMessage());
        }

        // Fallback: format data as simple text
        return $this->formatDataFallback($intent, $params, $data);
    }

    private function formatEmptyResult(string $intent, array $params): string
    {
        return match ($intent) {
            'stock_check'          => "❌ Tidak ditemukan stok untuk material/produk yang dicari. Coba periksa kembali nama atau kode materialnya ya.",
            'aging_check'          => "✅ Tidak ada produk aging yang ditemukan dengan kriteria tersebut.",
            'serial_number_lookup' => "❌ Serial number \"" . ($params['serial_number'] ?? '') . "\" tidak ditemukan di sistem kami. Coba periksa kembali nomornya.",
            'box_status'           => "❌ Box \"" . ($params['box_number'] ?? '') . "\" tidak ditemukan.",
            'po_status'            => "❌ PO \"" . ($params['po_number'] ?? '') . "\" tidak ditemukan.",
            'outbound_summary'     => "❌ Tidak ada data outbound untuk periode tersebut.",
            'product_history'      => "❌ Tidak ada riwayat untuk material tersebut.",
            'inventory_summary'    => "❌ Tidak dapat mengambil ringkasan inventori saat ini.",
            default                => "❌ Tidak ada data yang ditemukan untuk permintaan ini.",
        };
    }

    private function formatDataFallback(string $intent, array $params, array $data): string
    {
        $count = count($data);
        $preview = json_encode(array_slice($data, 0, 5), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "📊 Ditemukan **{$count}** hasil:\n```json\n{$preview}\n```";
    }

    // ========================================================================
    // System Prompts
    // ========================================================================

    private function getIntentClassificationPrompt(): string
    {
        return <<<PROMPT
Kamu adalah sistem klasifikasi intent untuk Warehouse Management System (WMS) Trans Kargo Indonesia.
Tugasmu: klasifikasikan pertanyaan user ke dalam salah satu intent berikut dan ekstrak parameternya.

Intent yang tersedia:
1. stock_check - Cek stok produk/material. Params: material, product_name
2. aging_check - Cek produk aging. Params: days (default 90), customer_name
3. serial_number_lookup - Cari serial number. Params: serial_number
4. box_status - Cek status box/PA. Params: box_number
5. po_status - Cek status Purchase Order. Params: po_number
6. outbound_summary - Ringkasan outbound. Params: customer_name, month, year
7. inventory_summary - Ringkasan inventori. Params: type (inv/gr/pm/spare)
8. product_history - Riwayat pergerakan produk. Params: material
9. general_chat - Pertanyaan umum/FAQ/sapaan. Params: []

Di akhir response, berikan JSON dengan format:
{"intent": "nama_intent", "params": {"key": "value"}}

JANGAN tambahkan teks apapun selain JSON di atas.
PROMPT;
    }

    private function getGeneralChatPrompt(): string
    {
        return <<<PROMPT
Kamu adalah AI Assistant untuk Warehouse Management System Trans Kargo Indonesia (WMS 3PL).
Nama kamu: "TKS AI Assistant".
Kamu membantu user dengan pertanyaan seputar gudang, inventori, dan penggunaan aplikasi WMS.

Konteks sistem:
- Aplikasi WMS untuk manajemen gudang pihak ketiga (3PL)
- Modul: Inbound (PO, QC, Put Away), Inventory, Outbound, General/PM/Spare Room
- Fitur utama: manajemen stok, tracking serial number, produk aging, transfer lokasi, cycle count
- Data master: Customer, Vendor, Warehouse, Storage (Area/Rak/Bin), Product/Material

Batasan:
- Jawab dalam Bahasa Indonesia yang ramah dan profesional
- Jika user bertanya data spesifik (stok, SN, box, PO), arahkan untuk bertanya dengan detail
- Jangan mengarang data — cukup bilang "saya perlu mencarikan datanya dulu" jika tidak yakin
- Jawaban singkat dan to the point, maksimal 2-3 paragraf

Kamu hanya menjawab pertanyaan terkait warehouse/gudang dan sistem WMS.
PROMPT;
    }

    private function getDataFormattingPrompt(string $intent): string
    {
        return <<<PROMPT
Kamu adalah AI Assistant untuk Warehouse Management System Trans Kargo Indonesia.
Tugasmu: ubah data query menjadi jawaban natural dalam Bahasa Indonesia.

Aturan:
1. Jawab dalam Bahasa Indonesia yang ramah, natural, dan mudah dipahami
2. Gunakan format yang rapi: sebutkan angka, nama, dan detail penting
3. Jika data banyak, rangkum poin-poin utamanya saja (maks 5-7 poin)
4. Gunakan emoji secukupnya untuk memperjelas (📦 stok, ⚠️ aging, 🔍 SN, 📋 PO, 🚚 outbound)
5. Jika ada data yang perlu perhatian khusus (aging > 90 hari, stok kosong), highlight
6. Akhiri dengan tawaran bantuan jika relevan ("Ada yang bisa saya bantu lagi?")

Jangan menyebutkan "data query" atau istilah teknis database dalam jawabanmu.
PROMPT;
    }

    // ========================================================================
    // Helpers
    // ========================================================================

    private function getRecentContext(ChatConversation $conversation, int $limit = 6): array
    {
        $messages = $conversation->messages()
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->reverse();

        $context = [];
        foreach ($messages as $msg) {
            $context[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
            ];
        }

        return $context;
    }

    private function parseJsonResponse(string $content): array
    {
        // Clean markdown code blocks if present
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*\n?/', '', $content);
        $content = preg_replace('/\n?```$/', '', $content);

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $intent = $decoded['intent'] ?? 'general_chat';
            $params = $decoded['params'] ?? [];

            // Validate intent
            if (!in_array($intent, self::INTENTS, true)) {
                $intent = 'general_chat';
            }

            return ['intent' => $intent, 'params' => $params];
        }

        // Fallback: treat as general chat
        return ['intent' => 'general_chat', 'params' => []];
    }
}
