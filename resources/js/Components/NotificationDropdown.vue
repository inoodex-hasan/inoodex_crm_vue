<template>
    <div class="relative" ref="dropdownRef">
        <button
            type="button"
            class="relative w-9 h-9 flex-none text-[#0e1726] dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex items-center justify-center rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 hover:bg-primary/10 dark:hover:bg-primary/20 transition shadow-xs"
            @click="isOpen = !isOpen"
            title="Notifications"
        >
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2Z"
                    stroke="currentColor"
                    stroke-width="2"
                />
                <path
                    d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-danger text-[10px] font-bold text-white shadow-xs"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 rounded-md shadow-xl bg-white dark:bg-[#1b2e4b] ring-1 ring-black ring-opacity-5 py-2 z-50 animate__animated animate__fadeIn"
        >
            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <span class="text-sm font-bold text-gray-900 dark:text-white">Notifications</span>
                <a
                    v-if="unreadCount > 0"
                    href="/dashboard/notifications/read-all"
                    class="text-xs text-primary hover:underline font-semibold"
                >
                    Mark all read
                </a>
            </div>

            <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
                <div
                    v-for="item in notifications"
                    :key="item.id"
                    class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-dark/20 transition cursor-pointer text-xs"
                    @click="markRead(item)"
                >
                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ item.data?.title || item.data?.message || 'New Notification' }}
                    </p>
                    <span class="text-[10px] text-gray-400 mt-1 block">
                        {{ formatDate(item.created_at) }}
                    </span>
                </div>

                <div v-if="!notifications?.length" class="p-6 text-center text-xs text-gray-400">
                    No new notifications
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const isOpen = ref(false);
const dropdownRef = ref(null);

const unreadCount = computed(() => page.props.auth?.user?.unread_notifications_count || 0);
const notifications = computed(() => page.props.auth?.user?.recent_notifications || []);

function markRead(item) {
    router.get(`/dashboard/notifications/${item.id}/read`);
    isOpen.value = false;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function handleClickOutside(event) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
