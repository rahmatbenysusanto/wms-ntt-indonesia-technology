<!-- AI Chat Widget - Floating -->
<div x-data="aiChatWidget" x-show="loaded" x-cloak
    class="ai-chat-widget"
    :class="{ 'open': isOpen }">

    <!-- Floating Button -->
    <button @click="toggleChat" class="ai-chat-fab"
        :class="{ 'd-none': isOpen }"
        title="TKS AI Assistant">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <path d="M8 9h8" stroke-width="1.5"/>
            <path d="M8 13h6" stroke-width="1.5"/>
        </svg>
        <span class="ai-fab-badge" x-show="unreadCount > 0" x-text="unreadCount"></span>
    </button>

    <!-- Chat Panel -->
    <div class="ai-chat-panel" x-show="isOpen" x-transition:enter="ai-chat-enter" x-transition:leave="ai-chat-leave">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="d-flex align-items-center gap-2">
                <button @click="showSidebar = !showSidebar" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle d-lg-none" title="Conversations">
                    <i class="mdi mdi-menu"></i>
                </button>
                <div class="ai-chat-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4z"/>
                        <path d="M16 14H8a6 6 0 0 0-6 6v1h20v-1a6 6 0 0 0-6-6z"/>
                        <circle cx="12" cy="8" r="2" fill="currentColor" stroke="none"/>
                    </svg>
                </div>
                <div>
                    <h6 class="mb-0 ai-chat-title">TKS AI Assistant</h6>
                    <small class="ai-chat-subtitle" x-text="isTyping ? 'Mengetik...' : 'Online'"></small>
                </div>
            </div>
            <div class="d-flex gap-1">
                <button @click="startNewChat" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" title="Chat Baru">
                    <i class="mdi mdi-plus"></i>
                </button>
                <button @click="isOpen = false" class="btn btn-sm btn-icon btn-ghost-secondary rounded-circle" title="Tutup">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
        </div>

        <div class="ai-chat-body">
            <!-- Sidebar (Conversation History) -->
            <div class="ai-chat-sidebar" :class="{ 'show': showSidebar }">
                <div class="ai-chat-sidebar-header">
                    <button @click="startNewChat" class="btn btn-sm btn-primary w-100">
                        <i class="mdi mdi-plus"></i> Chat Baru
                    </button>
                </div>
                <div class="ai-chat-sidebar-list" id="conversationList">
                    <template x-for="conv in conversations" :key="conv.id">
                        <div class="ai-chat-conv-item"
                            :class="{ 'active': conv.id === currentConversationId }"
                            @click="loadConversation(conv.id)">
                            <div class="ai-chat-conv-title" x-text="conv.title"></div>
                            <div class="ai-chat-conv-meta">
                                <small x-text="conv.updated_at"></small>
                                <button @click.stop="deleteConversation(conv.id)"
                                    class="btn btn-sm btn-icon btn-ghost-danger rounded-circle"
                                    title="Hapus">
                                    <i class="mdi mdi-delete-outline fs-14"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div x-show="conversations.length === 0" class="text-center text-muted py-4">
                        <small>Belum ada percakapan</small>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="ai-chat-messages" id="chatMessages" x-ref="messagesContainer">
                <!-- Welcome / Empty State -->
                <div x-show="messages.length === 0 && !isTyping" class="ai-chat-welcome">
                    <div class="ai-chat-welcome-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h6>Halo! 👋</h6>
                    <p>Saya TKS AI Assistant, siap membantu Anda dengan:</p>
                    <div class="ai-chat-suggestions">
                        <button @click="sendSuggestion('Cek stok material')" class="ai-suggestion-chip">📦 Cek stok</button>
                        <button @click="sendSuggestion('Produk aging lebih dari 90 hari')" class="ai-suggestion-chip">⚠️ Cek aging</button>
                        <button @click="sendSuggestion('Cari serial number')" class="ai-suggestion-chip">🔍 Cari SN</button>
                        <button @click="sendSuggestion('Status box PA-')" class="ai-suggestion-chip">📋 Cek box</button>
                        <button @click="sendSuggestion('Ringkasan outbound bulan ini')" class="ai-suggestion-chip">🚚 Outbound</button>
                        <button @click="sendSuggestion('Status PO')" class="ai-suggestion-chip">📑 Status PO</button>
                    </div>
                </div>

                <!-- Message Bubbles -->
                <template x-for="msg in messages" :key="msg.id">
                    <div class="ai-chat-bubble" :class="msg.role">
                        <div class="ai-chat-bubble-avatar" x-show="msg.role === 'assistant'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4z"/>
                                <circle cx="12" cy="8" r="2" fill="currentColor" stroke="none"/>
                            </svg>
                        </div>
                        <div class="ai-chat-bubble-content" x-html="formatMessage(msg.content)"></div>
                        <div class="ai-chat-bubble-time" x-text="msg.created_at"></div>
                    </div>
                </template>

                <!-- Typing Indicator -->
                <div x-show="isTyping" class="ai-chat-bubble assistant">
                    <div class="ai-chat-bubble-avatar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4z"/>
                            <circle cx="12" cy="8" r="2" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>
                    <div class="ai-chat-bubble-content">
                        <div class="ai-typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="ai-chat-footer">
            <div class="ai-chat-input-group">
                <textarea
                    x-model="inputMessage"
                    @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                    @keydown.escape="isOpen = false"
                    placeholder="Ketik pertanyaan Anda..."
                    rows="1"
                    class="ai-chat-input"
                    :disabled="isTyping"
                    x-ref="messageInput"
                    @input="autoResize($el)"
                ></textarea>
                <button @click="sendMessage"
                    class="btn btn-primary btn-sm btn-icon rounded-circle ai-send-btn"
                    :disabled="isTyping || !inputMessage.trim()"
                    title="Kirim">
                    <i class="mdi" :class="isTyping ? 'mdi-timer-sand' : 'mdi-send'"></i>
                </button>
            </div>
            <div class="ai-chat-disclaimer">
                <small>⚡ Powered by DeepSeek AI · Jawaban bisa dicek ulang</small>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== AI Chat Widget Styles ===== */
.ai-chat-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: var(--bs-body-font-family, 'Inter', sans-serif);
}

[x-cloak] { display: none !important; }

/* Floating Action Button */
.ai-chat-fab {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}
.ai-chat-fab:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}
.ai-fab-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

/* Chat Panel */
.ai-chat-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 420px;
    height: 600px;
    max-height: calc(100vh - 120px);
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
@media (max-width: 480px) {
    .ai-chat-panel {
        width: calc(100vw - 20px);
        right: -8px;
        bottom: 70px;
        height: 500px;
    }
}

/* Transitions */
.ai-chat-enter { animation: aiChatSlideUp 0.25s ease-out; }
.ai-chat-leave { animation: aiChatSlideDown 0.2s ease-in; }
@keyframes aiChatSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes aiChatSlideDown {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to { opacity: 0; transform: translateY(20px) scale(0.95); }
}

/* Header */
.ai-chat-header {
    padding: 14px 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.ai-chat-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.ai-chat-title { font-size: 14px; font-weight: 600; line-height: 1.2; }
.ai-chat-subtitle { font-size: 11px; opacity: 0.8; }

/* Body */
.ai-chat-body {
    display: flex;
    flex: 1;
    overflow: hidden;
    position: relative;
}

/* Sidebar */
.ai-chat-sidebar {
    width: 260px;
    border-right: 1px solid #e9ecef;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: transform 0.25s ease;
}
@media (max-width: 576px) {
    .ai-chat-sidebar {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        z-index: 10;
        transform: translateX(-100%);
    }
    .ai-chat-sidebar.show {
        transform: translateX(0);
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    }
}
.ai-chat-sidebar-header {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
}
.ai-chat-sidebar-list {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}
.ai-chat-conv-item {
    padding: 10px 12px;
    border-radius: 10px;
    cursor: pointer;
    margin-bottom: 4px;
    transition: background 0.15s ease;
}
.ai-chat-conv-item:hover { background: #e9ecef; }
.ai-chat-conv-item.active { background: #667eea1a; border: 1px solid #667eea33; }
.ai-chat-conv-title {
    font-size: 13px;
    font-weight: 500;
    color: #212529;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ai-chat-conv-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    color: #6c757d;
    margin-top: 2px;
}

/* Messages Area */
.ai-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f9fa;
    scroll-behavior: smooth;
}

/* Welcome */
.ai-chat-welcome {
    text-align: center;
    padding: 30px 10px;
}
.ai-chat-welcome-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea15, #764ba215);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #667eea;
}
.ai-chat-welcome h6 { font-size: 16px; margin-bottom: 6px; }
.ai-chat-welcome p { font-size: 13px; color: #6c757d; margin-bottom: 16px; }
.ai-chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}
.ai-suggestion-chip {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #dee2e6;
    background: #fff;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    color: #495057;
}
.ai-suggestion-chip:hover {
    background: #667eea;
    color: #fff;
    border-color: #667eea;
}

/* Message Bubbles */
.ai-chat-bubble {
    display: flex;
    flex-direction: column;
    margin-bottom: 16px;
    animation: aiBubbleIn 0.3s ease-out;
}
@keyframes aiBubbleIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.ai-chat-bubble.user { align-items: flex-end; }
.ai-chat-bubble.assistant { align-items: flex-start; flex-direction: row; gap: 8px; }

.ai-chat-bubble-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}
.ai-chat-bubble-content {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.55;
    word-wrap: break-word;
}
.ai-chat-bubble.user .ai-chat-bubble-content {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.ai-chat-bubble.assistant .ai-chat-bubble-content {
    background: #fff;
    color: #212529;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.ai-chat-bubble-content strong { font-weight: 600; }
.ai-chat-bubble-content em { font-style: italic; }
.ai-chat-bubble-content code {
    background: rgba(0,0,0,0.06);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}
.ai-chat-bubble.user .ai-chat-bubble-content code {
    background: rgba(255,255,255,0.2);
}
.ai-chat-bubble-time {
    font-size: 10px;
    color: #adb5bd;
    margin-top: 2px;
    padding: 0 4px;
}
.ai-chat-bubble.user .ai-chat-bubble-time { text-align: right; }

/* Typing Indicator */
.ai-typing-dots {
    display: flex;
    gap: 4px;
    padding: 4px 0;
}
.ai-typing-dots span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #adb5bd;
    animation: aiTyping 1.4s infinite ease-in-out;
}
.ai-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes aiTyping {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}

/* Footer / Input */
.ai-chat-footer {
    padding: 12px 16px;
    border-top: 1px solid #e9ecef;
    background: #fff;
    flex-shrink: 0;
}
.ai-chat-input-group {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #f1f3f5;
    border-radius: 24px;
    padding: 6px 8px 6px 16px;
    border: 1px solid #e9ecef;
    transition: border-color 0.15s ease;
}
.ai-chat-input-group:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
}
.ai-chat-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 13px;
    resize: none;
    max-height: 100px;
    line-height: 1.5;
    padding: 4px 0;
    color: #212529;
}
.ai-chat-input::placeholder { color: #adb5bd; }
.ai-chat-input:disabled { opacity: 0.6; }
.ai-send-btn {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #667eea, #764ba2) !important;
    border: none !important;
    transition: all 0.2s ease;
}
.ai-send-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}
.ai-send-btn:not(:disabled):hover {
    transform: scale(1.1);
}
.ai-chat-disclaimer {
    text-align: center;
    margin-top: 6px;
    font-size: 10px;
    color: #adb5bd;
}

/* Dark mode support */
[data-bs-theme="dark"] .ai-chat-panel {
    background: #1e1e2d;
}
[data-bs-theme="dark"] .ai-chat-sidebar {
    background: #1a1a27;
    border-color: #2d2d3f;
}
[data-bs-theme="dark"] .ai-chat-conv-item:hover {
    background: #2d2d3f;
}
[data-bs-theme="dark"] .ai-chat-conv-title {
    color: #e9ecef;
}
[data-bs-theme="dark"] .ai-chat-messages {
    background: #1a1a27;
}
[data-bs-theme="dark"] .ai-chat-bubble.assistant .ai-chat-bubble-content {
    background: #2d2d3f;
    color: #e9ecef;
}
[data-bs-theme="dark"] .ai-chat-footer {
    background: #1e1e2d;
    border-color: #2d2d3f;
}
[data-bs-theme="dark"] .ai-chat-input-group {
    background: #2d2d3f;
    border-color: #3d3d4f;
}
[data-bs-theme="dark"] .ai-chat-input {
    color: #e9ecef;
}
[data-bs-theme="dark"] .ai-suggestion-chip {
    background: #2d2d3f;
    border-color: #3d3d4f;
    color: #ced4da;
}
[data-bs-theme="dark"] .ai-chat-sidebar-header {
    border-color: #2d2d3f;
}
[data-bs-theme="dark"] .ai-chat-sidebar {
    border-color: #2d2d3f;
}
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aiChatWidget', () => ({
        // State
        isOpen: false,
        loaded: true,
        showSidebar: window.innerWidth > 576,
        isTyping: false,
        unreadCount: 0,
        currentConversationId: null,
        inputMessage: '',
        messages: [],
        conversations: [],

        // Initialize
        init() {
            this.loadConversations();
        },

        // Toggle chat panel
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.$refs.messageInput?.focus();
                    this.scrollToBottom();
                });
            }
        },

        // Load conversation list
        async loadConversations() {
            try {
                const res = await fetch('/ai-chat/conversations');
                const data = await res.json();
                if (data.success) {
                    this.conversations = data.conversations;
                }
            } catch (e) {
                console.error('Failed to load conversations:', e);
            }
        },

        // Load a conversation's messages
        async loadConversation(id) {
            if (this.currentConversationId === id) return;
            this.currentConversationId = id;
            this.messages = [];
            this.showSidebar = window.innerWidth > 576;

            try {
                const res = await fetch(`/ai-chat/messages/${id}`);
                const data = await res.json();
                if (data.success) {
                    this.messages = data.messages;
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (e) {
                console.error('Failed to load messages:', e);
            }
        },

        // Start new chat
        startNewChat() {
            this.currentConversationId = null;
            this.messages = [];
            this.showSidebar = window.innerWidth > 576;
            this.$nextTick(() => this.$refs.messageInput?.focus());
        },

        // Send message
        async sendMessage() {
            const msg = this.inputMessage.trim();
            if (!msg || this.isTyping) return;

            // Add user message locally
            const tempId = 'temp-' + Date.now();
            this.messages.push({
                id: tempId,
                role: 'user',
                content: msg,
                created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})
            });
            this.inputMessage = '';
            this.$nextTick(() => this.scrollToBottom());

            // Send to server
            this.isTyping = true;
            try {
                const res = await fetch('/ai-chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        message: msg,
                        chat_conversation_id: this.currentConversationId,
                    }),
                });
                const data = await res.json();

                if (data.success) {
                    // Remove temp message, add real ones
                    this.messages = this.messages.filter(m => m.id !== tempId);
                    this.messages.push({
                        id: 'u-' + Date.now(),
                        role: 'user',
                        content: msg,
                        created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})
                    });
                    this.messages.push({
                        id: 'a-' + Date.now(),
                        role: 'assistant',
                        content: data.reply,
                        created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})
                    });

                    this.currentConversationId = data.chat_conversation_id;

                    // Refresh conversation list
                    await this.loadConversations();
                } else {
                    this.messages.push({
                        id: 'err-' + Date.now(),
                        role: 'assistant',
                        content: '❌ Maaf, terjadi kesalahan. Silakan coba lagi.',
                        created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})
                    });
                }
            } catch (e) {
                console.error('Failed to send message:', e);
                this.messages.push({
                    id: 'err-' + Date.now(),
                    role: 'assistant',
                    content: '❌ Gagal terhubung ke server. Periksa koneksi Anda.',
                    created_at: new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})
                });
            } finally {
                this.isTyping = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        // Send a suggestion chip
        sendSuggestion(text) {
            this.inputMessage = text;
            this.sendMessage();
        },

        // Delete conversation
        async deleteConversation(id) {
            if (!confirm('Hapus percakapan ini?')) return;
            try {
                await fetch(`/ai-chat/conversation/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
                if (this.currentConversationId === id) {
                    this.startNewChat();
                }
                await this.loadConversations();
            } catch (e) {
                console.error('Failed to delete conversation:', e);
            }
        },

        // Auto-resize textarea
        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 100) + 'px';
        },

        // Scroll messages to bottom
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        // Format markdown in messages
        formatMessage(content) {
            if (!content) return '';
            // Bold
            content = content.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            // Italic
            content = content.replace(/\*(.+?)\*/g, '<em>$1</em>');
            // Inline code
            content = content.replace(/`(.+?)`/g, '<code>$1</code>');
            // Newlines to <br>
            content = content.replace(/\n/g, '<br>');
            return content;
        },
    }));
});
</script>
