{{-- DocBot AI Chatbot Widget - Fixed floating button bottom-right --}}
<div
    x-data="chatbotWidget()"
    x-init="init()"
    class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 flex flex-col items-end"
    x-cloak
>
    {{-- ================================================================
         CHAT PANEL
    ================================================================ --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="mb-3 sm:mb-4 w-[calc(100vw-2rem)] sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden"
        style="height: min(520px, calc(100vh - 7rem)); max-height: calc(100vh - 7rem); display: none;"
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm leading-tight">DocBot</p>
                    <p class="text-blue-100 text-xs leading-tight">AI Document Assistant</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button
                    @click="clearChat()"
                    title="Clear conversation"
                    class="p-1.5 text-blue-200 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                <button
                    @click="open = false"
                    title="Close chat"
                    class="p-1.5 text-blue-200 hover:text-white hover:bg-white/10 rounded-lg transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div
            class="flex-1 overflow-y-auto px-4 py-3 space-y-4 bg-slate-50 min-h-0"
            x-ref="messagesContainer"
        >
            {{-- Welcome state (no messages yet) --}}
            <template x-if="messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-center py-8">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <p class="text-slate-700 font-medium text-sm">Hello! I'm DocBot.</p>
                    <p class="text-slate-500 text-xs mt-1 max-w-xs leading-relaxed">Ask me to search documents, summarize content, or help you navigate DocTrack.</p>
                    <div class="mt-4 flex flex-wrap gap-2 justify-center">
                        <button @click="sendSuggestion('Show my pending documents')"
                                class="text-xs bg-red-50 text-red-700 border border-red-200 rounded-full px-3 py-1 hover:bg-red-100 transition-colors">
                            My pending docs
                        </button>
                        <button @click="sendSuggestion('Show recent documents')"
                                class="text-xs bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-3 py-1 hover:bg-blue-100 transition-colors">
                            Recent documents
                        </button>
                        <button @click="sendSuggestion('Show my document statistics')"
                                class="text-xs bg-green-50 text-green-700 border border-green-200 rounded-full px-3 py-1 hover:bg-green-100 transition-colors">
                            My stats
                        </button>
                        <button @click="sendSuggestion('Read document content')"
                                class="text-xs bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-3 py-1 hover:bg-amber-100 transition-colors">
                            Read doc content
                        </button>
                        <button @click="sendSuggestion('How do I forward a document?')"
                                class="text-xs bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-3 py-1 hover:bg-purple-100 transition-colors">
                            How to forward?
                        </button>
                        <button @click="sendSuggestion('How do I upload a document?')"
                                class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 hover:bg-indigo-100 transition-colors">
                            How to upload?
                        </button>
                    </div>
                </div>
            </template>

            {{-- Message list --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start items-start gap-2'">
                    {{-- Bot avatar --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                    </template>

                    <div class="max-w-[80%]">
                        {{-- Bubble --}}
                        <div
                            :class="msg.role === 'user'
                                ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 text-sm'
                                : 'bg-white text-slate-800 rounded-2xl rounded-tl-sm px-4 py-2.5 text-sm shadow-sm border border-slate-100'"
                        >
                            <p class="whitespace-pre-wrap leading-relaxed text-sm" x-html="formatMessage(msg.content)"></p>
                        </div>

                        {{-- Document link cards --}}
                        <template x-if="msg.documents && msg.documents.length > 0">
                            <div class="mt-2 space-y-1.5">
                                <p class="text-xs text-slate-500 font-medium px-1">Related documents:</p>
                                <template x-for="doc in msg.documents" :key="doc.id">
                                    <a
                                        :href="doc.url"
                                        class="flex items-center gap-2 bg-white border border-blue-200 rounded-lg px-3 py-2 hover:bg-blue-50 hover:border-blue-400 transition-colors group"
                                    >
                                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs text-blue-700 font-medium group-hover:underline truncate flex-1" x-text="doc.title"></span>
                                        <svg class="w-3 h-3 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <template x-if="loading">
                <div class="flex justify-start items-start gap-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-slate-100">
                        <div class="flex gap-1 items-center">
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                            <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Error banner --}}
            <template x-if="error">
                <div class="flex justify-center">
                    <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl px-4 py-2 flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="error"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Input area --}}
        <div class="px-4 py-3 bg-white border-t border-slate-100 flex-shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
                <textarea
                    x-ref="messageInput"
                    x-model="inputText"
                    @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                    :disabled="loading"
                    placeholder="Ask about documents or how to use DocTrack..."
                    rows="1"
                    class="flex-1 resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-slate-50 disabled:text-slate-400 transition-colors"
                    style="max-height:120px; overflow-y:auto;"
                    @input="autoResize($refs.messageInput)"
                ></textarea>
                <button
                    type="submit"
                    :disabled="loading || inputText.trim() === ''"
                    class="w-10 h-10 flex-shrink-0 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-200 disabled:cursor-not-allowed text-white rounded-xl flex items-center justify-center transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <p class="text-xs text-slate-400 mt-1.5 text-center select-none">Enter to send &middot; Shift+Enter for new line</p>
        </div>
    </div>

    {{-- ================================================================
         TOGGLE BUTTON
    ================================================================ --}}
    <button
        @click="open = !open"
        title="Chat with DocBot"
        class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-200 relative"
    >
        <svg x-show="!open" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <svg x-show="open" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span x-ref="pulseRing" x-show="!open" class="absolute inset-0 rounded-full bg-blue-400 opacity-30 animate-ping pointer-events-none"></span>
    </button>
</div>

<script>
function chatbotWidget() {
    const STORAGE_KEY = 'docbot_chat_session';

    return {
        open: false,
        loading: false,
        error: null,
        inputText: '',
        messages: [],

        init() {
            // Restore chat session from sessionStorage
            this.loadSession();

            // Stop ping animation after 5 seconds
            setTimeout(() => {
                if (this.$refs.pulseRing) {
                    this.$refs.pulseRing.style.display = 'none';
                }
            }, 5000);

            // Auto-scroll if there are restored messages and panel is open
            if (this.messages.length > 0 && this.open) {
                this.$nextTick(() => this.scrollToBottom());
            }

            // Watch for changes to save session
            this.$watch('messages', () => this.saveSession(), { deep: true });
            this.$watch('open', (val) => {
                this.saveSession();
                if (val && this.messages.length > 0) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            });
        },

        loadSession() {
            try {
                const saved = sessionStorage.getItem(STORAGE_KEY);
                if (saved) {
                    const data = JSON.parse(saved);
                    if (data && Array.isArray(data.messages)) {
                        this.messages = data.messages;
                    }
                    if (typeof data.open === 'boolean') {
                        this.open = data.open;
                    }
                }
            } catch (e) {
                console.warn('DocBot: Failed to restore session', e);
            }
        },

        saveSession() {
            try {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                    messages: this.messages,
                    open: this.open,
                }));
            } catch (e) {
                console.warn('DocBot: Failed to save session', e);
            }
        },

        sendSuggestion(text) {
            this.inputText = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.inputText.trim();
            if (!text || this.loading) return;

            this.messages.push({ role: 'user', content: text, documents: [] });
            this.inputText = '';
            this.error = null;

            if (this.$refs.messageInput) {
                this.$refs.messageInput.style.height = 'auto';
            }

            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            // Last N turns for context (exclude the message we just added)
            // Truncate each history turn to reduce request payload size
            const MAX_HISTORY_CHARS = 1500;
            const history = this.messages
                .slice(0, -1)
                .slice(-4)
                .map(m => ({ role: m.role, content: m.content.substring(0, MAX_HISTORY_CHARS) }));

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const response = await fetch('{{ route("chatbot.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text, history }),
                });

                if (response.status === 429) {
                    this.error = 'Too many requests. Please wait a moment before sending another message.';
                    this.messages.pop();
                    this.inputText = text;
                    this.loading = false;
                    return;
                }

                if (!response.ok) {
                    throw new Error('Server returned status ' + response.status);
                }

                const data = await response.json();

                this.messages.push({
                    role: 'assistant',
                    content: data.reply || 'I could not generate a response.',
                    documents: data.documents || [],
                });

            } catch (err) {
                console.error('DocBot error:', err);
                this.error = 'Something went wrong. Please try again.';
                // Restore user message to retry
                this.messages.pop();
                this.inputText = text;
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        clearChat() {
            this.messages = [];
            this.error = null;
            this.inputText = '';
            this.saveSession();
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        },

        formatMessage(text) {
            if (!text) return '';
            // Escape HTML to prevent XSS
            let escaped = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
            // Convert **bold** markdown
            escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            // Convert *italic* markdown
            escaped = escaped.replace(/\*(.+?)\*/g, '<em>$1</em>');
            // Convert `code` markdown
            escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-xs">$1</code>');
            // Convert bullet list items (- or •)
            escaped = escaped.replace(/^[\-•]\s+(.+)$/gm, '<span class="flex gap-1.5 items-start"><span class="text-blue-500 mt-0.5">•</span><span>$1</span></span>');
            // Convert numbered list items
            escaped = escaped.replace(/^(\d+)\.\s+(.+)$/gm, '<span class="flex gap-1.5 items-start"><span class="text-blue-600 font-semibold">$1.</span><span>$2</span></span>');
            // Convert newlines to <br>
            return escaped.replace(/\n/g, '<br>');
        },
    };
}
</script>
