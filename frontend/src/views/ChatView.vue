<script setup>
import { onMounted, ref, watch, nextTick } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const route = useRoute();
const conversations = ref([]);
const activeConversation = ref(null);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(true);
const loadingMessages = ref(false);
const messageContainer = ref(null);
const user = ref(JSON.parse(localStorage.getItem('user') || '{}'));

const fetchConversations = async () => {
    try {
        const response = await axios.get('http://localhost:8000/api/conversations', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        conversations.value = response.data;
        
        // If there's a conversation ID in the query, select it
        if (route.query.id) {
            const conv = conversations.value.find(c => c.id == route.query.id);
            if (conv) selectConversation(conv);
        } else if (conversations.value.length > 0) {
            selectConversation(conversations.value[0]);
        }
    } catch (e) {
        console.error('Failed to load conversations', e);
    } finally {
        loading.value = false;
    }
};

const selectConversation = async (conversation) => {
    activeConversation.value = conversation;
    loadingMessages.value = true;
    try {
        const response = await axios.get(`http://localhost:8000/api/conversations/${conversation.id}`, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        messages.value = response.data;
        await scrollToBottom();
    } catch (e) {
        console.error('Failed to load messages', e);
    } finally {
        loadingMessages.value = false;
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || !activeConversation.value) return;

    const content = newMessage.value;
    newMessage.value = '';

    try {
        const response = await axios.post(`http://localhost:8000/api/conversations/${activeConversation.value.id}/messages`, 
        { content },
        { headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` } });
        
        messages.value.push({
            ...response.data,
            sender: user.value
        });
        await scrollToBottom();
    } catch (e) {
        console.error('Failed to send message', e);
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
};

onMounted(fetchConversations);

const getOtherUser = (conv) => {
    if (!conv) return {};
    return user.value.role === 'employer' ? conv.seeker : conv.employer;
};
</script>

<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 h-[calc(100vh-120px)]">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg h-full flex">
            <!-- Conversations List -->
            <div class="w-1/3 border-r border-gray-200 flex flex-col">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-900">Messages</h2>
                </div>
                <div class="flex-1 overflow-y-auto">
                    <div v-if="loading" class="p-4 text-center">Loading...</div>
                    <div v-else-if="conversations.length === 0" class="p-4 text-center text-gray-500">
                        No conversations yet.
                    </div>
                    <ul v-else class="divide-y divide-gray-200">
                        <li v-for="conv in conversations" :key="conv.id" 
                            @click="selectConversation(conv)"
                            :class="['p-4 cursor-pointer hover:bg-gray-50 transition', activeConversation?.id === conv.id ? 'bg-indigo-50' : '']">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                        {{ getOtherUser(conv).name?.charAt(0) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ getOtherUser(conv).name }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate" v-if="conv.job_listing">
                                        Re: {{ conv.job_listing.title }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 truncate" v-if="conv.messages?.length > 0">
                                        {{ conv.messages[0].content }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Chat Window -->
            <div class="flex-1 flex flex-col bg-gray-50">
                <div v-if="activeConversation" class="h-full flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-gray-200 bg-white shadow-sm flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ getOtherUser(activeConversation).name?.charAt(0) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ getOtherUser(activeConversation).name }}</h3>
                                <p class="text-xs text-gray-500" v-if="activeConversation.job_listing">
                                    Regarding: {{ activeConversation.job_listing.title }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div ref="messageContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <div v-if="loadingMessages" class="text-center py-4">Loading messages...</div>
                        <template v-else>
                            <div v-for="msg in messages" :key="msg.id" 
                                :class="['flex', msg.sender_id === user.id ? 'justify-end' : 'justify-start']">
                                <div :class="['max-w-xs px-4 py-2 rounded-lg shadow-sm', 
                                    msg.sender_id === user.id ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900']">
                                    <p class="text-sm">{{ msg.content }}</p>
                                    <p :class="['text-[10px] mt-1', msg.sender_id === user.id ? 'text-indigo-200' : 'text-gray-400']">
                                        {{ new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                                    </p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Area -->
                    <div class="p-4 bg-white border-t border-gray-200">
                        <form @submit.prevent="sendMessage" class="flex space-x-3">
                            <input v-model="newMessage" type="text" placeholder="Type a message..." 
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send
                            </button>
                        </form>
                    </div>
                </div>
                <div v-else class="flex-1 flex items-center justify-center text-gray-500">
                    Select a conversation to start chatting
                </div>
            </div>
        </div>
    </div>
</template>
