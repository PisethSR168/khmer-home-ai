@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="aiAssistantChat()">

    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
            <i data-lucide="bot" class="w-4 h-4 text-emerald-600"></i>
            24/7 AI Concierge
        </span>
        <h1 class="text-3xl font-extrabold text-slate-900 mt-2">Room SR AI Rental Assistant</h1>
        <p class="text-slate-500 text-sm mt-1">
            Ask any question about rental rooms ($30 to $100/mo), EDC electricity rates ($0.20/kWh), Siem Reap communes, or let AI recommend verified rooms.
        </p>
    </div>

    <!-- Chat Interface Container -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden flex flex-col h-[650px]">

        <!-- Chat Header Bar -->
        <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-slate-950 font-bold">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Room SR AI Advisor (ក្រុងសៀមរាប)</h3>
                    <div class="text-[11px] text-emerald-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Connected to Live Database ($30 - $100/mo Rooms)
                    </div>
                </div>
            </div>

            <button @click="clearChat()" class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Clear
            </button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chat-messages" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/50">
            
            <template x-for="(msg, index) in messages" :key="index">
                <div>
                    <!-- User Message -->
                    <div x-show="msg.role === 'user'" class="flex gap-3 max-w-[80%] ml-auto flex-row-reverse">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            You
                        </div>
                        <div class="bg-emerald-600 text-white p-3.5 rounded-2xl rounded-tr-none text-xs sm:text-sm leading-relaxed shadow-sm">
                            <span x-text="msg.content"></span>
                        </div>
                    </div>

                    <!-- AI Assistant Message -->
                    <div x-show="msg.role === 'assistant'" class="flex gap-3 max-w-[85%]">
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0 border border-slate-700">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                        </div>
                        <div class="space-y-3">
                            <div class="bg-white border border-slate-200 p-4 rounded-2xl rounded-tl-none text-xs sm:text-sm text-slate-800 leading-relaxed shadow-sm whitespace-pre-line">
                                <span x-text="msg.content"></span>
                            </div>

                            <!-- Attached Real Rooms Recommendations (if any) -->
                            <template x-if="msg.rooms && msg.rooms.length > 0">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                    <template x-for="room in msg.rooms" :key="room.id">
                                        <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-xs hover:border-emerald-500 transition">
                                            <div class="font-bold text-xs text-slate-900 truncate" x-text="room.title"></div>
                                            <div class="text-[11px] text-emerald-600 font-bold mt-0.5" x-text="room.price"></div>
                                            <div class="text-[10px] text-slate-500 mt-0.5" x-text="room.district"></div>
                                            <a :href="room.link" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:underline">
                                                Open Listing Details <i data-lucide="external-link" class="w-3 h-3"></i>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="loading" class="flex gap-3 max-w-[80%]">
                <div class="w-8 h-8 rounded-full bg-slate-900 text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0">
                    <i data-lucide="bot" class="w-4 h-4"></i>
                </div>
                <div class="bg-white border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-bounce [animation-delay:0.4s]"></span>
                </div>
            </div>

        </div>

        <!-- Pre-suggested Prompts -->
        <div class="px-6 py-2 bg-slate-100/70 border-t border-slate-200 flex items-center gap-2 overflow-x-auto text-xs text-slate-600">
            <span class="font-bold text-slate-400 shrink-0">Suggestions:</span>
            <button @click="sendPrompt('Show affordable rooms $30 to $50 in Wat Bo and Chreav')" class="px-3 py-1 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 rounded-full shrink-0 transition">
                💰 Rooms $30 - $50
            </button>
            <button @click="sendPrompt('What are standard electricity (EDC) rates for rooms in Siem Reap?')" class="px-3 py-1 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 rounded-full shrink-0 transition">
                ⚡ EDC Electricity ($0.20/kWh)
            </button>
            <button @click="sendPrompt('Find air-conditioned room in Sala Kamreuk or Svay Dangkum under $100')" class="px-3 py-1 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 rounded-full shrink-0 transition">
                ❄️ Aircon Rooms &lt; $100
            </button>
            <button @click="sendPrompt('How much is the typical security deposit for a room in Siem Reap?')" class="px-3 py-1 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 rounded-full shrink-0 transition">
                📝 1-Month Deposit Rules
            </button>
        </div>

        <!-- Chat Input Form -->
        <div class="p-4 bg-white border-t border-slate-200">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text" x-model="userInput" :disabled="loading" placeholder="Ask about $30-$100 rooms, Wat Bo, Sala Kamreuk, EDC electricity..."
                       class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                <button type="submit" :disabled="loading || !userInput.trim()"
                        class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold text-xs sm:text-sm rounded-2xl shadow-md transition flex items-center gap-1.5">
                    <span>Send</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </div>

</div>

<script>
function aiAssistantChat() {
    return {
        userInput: '',
        loading: false,
        messages: [
            {
                role: 'assistant',
                content: 'Suasdei! I am your Room SR AI Rental Assistant in Siem Reap (ក្រុងសៀមរាប). I can help you find affordable rooms ($30 to $100/mo) in Wat Bo, Sala Kamreuk, Svay Dangkum, Chreav, and Kork Chak, check official EDC electricity rates (~$0.20/kWh), and connect you directly with verified local landlords. What room price or location are you looking for?',
                rooms: []
            }
        ],
        async sendMessage() {
            if (!this.userInput.trim() || this.loading) return;

            const text = this.userInput.trim();
            this.messages.push({ role: 'user', content: text, rooms: [] });
            this.userInput = '';
            this.loading = true;
            this.scrollToBottom();

            try {
                const response = await fetch('/api/v1/ai/rental-assistant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();
                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        content: data.data.reply,
                        rooms: data.data.suggested_rooms || []
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: 'I apologize, but I encountered an issue processing your query. Please try again.',
                        rooms: []
                    });
                }
            } catch (e) {
                this.messages.push({
                    role: 'assistant',
                    content: 'In Phnom Penh, we have rooms available from $140/mo in Tuol Kouk up to $650/mo condos in Sen Sok and BKK1. Feel free to browse our listings or check our search page!',
                    rooms: []
                });
            } finally {
                this.loading = false;
                this.scrollToBottom();
                setTimeout(() => {
                    if (window.lucide) window.lucide.createIcons();
                }, 50);
            }
        },
        sendPrompt(promptText) {
            this.userInput = promptText;
            this.sendMessage();
        },
        clearChat() {
            this.messages = [
                {
                    role: 'assistant',
                    content: 'Chat reset. How can I help you find a rental property in Cambodia today?',
                    rooms: []
                }
            ];
            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('chat-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }
}
</script>

@endsection
