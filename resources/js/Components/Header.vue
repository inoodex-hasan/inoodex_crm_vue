<template>
    <header class="z-40">
        <div class="shadow-sm">
            <div class="relative bg-white flex w-full items-center justify-between px-5 py-2.5 dark:bg-[#0e1726]">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <!-- Mobile Hamburger -->
                    <div class="flex lg:hidden items-center">
                        <button
                            type="button"
                            class="w-9 h-9 flex-none text-[#0e1726] dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex items-center justify-center rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 hover:bg-primary/10 dark:hover:bg-primary/20 transition shadow-xs"
                            @click="$emit('toggle-sidebar')"
                            title="Toggle Navigation"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 7L4 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M20 12L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.8" />
                                <path d="M20 17L4 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Desktop Toggle Button -->
                    <div class="hidden lg:flex items-center">
                        <button
                            type="button"
                            class="w-9 h-9 flex-none text-[#0e1726] dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex items-center justify-center rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 hover:bg-primary/10 dark:hover:bg-primary/20 transition shadow-xs"
                            @click="$emit('toggle-sidebar')"
                            title="Toggle Sidebar"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 7L4 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M20 12L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.8" />
                                <path d="M20 17L4 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Quick Dashboard Link -->
                    <div class="hidden sm:flex items-center">
                        <Link
                            href="/dashboard"
                            class="w-9 h-9 flex-none text-[#0e1726] dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex items-center justify-center rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 hover:bg-primary/10 dark:hover:bg-primary/20 transition shadow-xs"
                            title="Dashboard"
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Live Currency Exchange Rates Ticker -->
                    <CurrencyTicker :currencies="currencies" />
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3 rtl:space-x-reverse dark:text-[#d0d2d6]">
                    <!-- Theme Toggle -->
                    <div>
                        <button
                            type="button"
                            class="w-9 h-9 flex-none text-[#0e1726] dark:text-[#d0d2d6] hover:text-primary dark:hover:text-primary flex items-center justify-center rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 hover:bg-primary/10 dark:hover:bg-primary/20 transition shadow-xs"
                            @click="toggleTheme"
                            :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        >
                            <svg v-if="isDark" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" />
                                <path d="M12 2V4M12 20V22M4 12H2M22 12H20M19.07 4.93L17.66 6.34M6.34 17.66L4.93 19.07M19.07 19.07L17.66 17.66M6.34 6.34L4.93 4.93" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.0672 11.8568C20.9168 16.7329 16.8997 20.6778 12.0125 20.6778C6.98064 20.6778 2.89062 16.5878 2.89062 11.556C2.89062 6.66878 6.83556 2.6517 11.7116 2.50125C11.9688 2.49324 12.1643 2.73812 12.086 2.98394C11.1444 5.93297 11.7582 9.24823 13.9317 11.4218C16.1053 13.5953 19.4206 14.2091 22.3696 13.2675C22.6154 13.1892 22.8603 13.3847 22.8523 13.6419C22.8123 14.921 22.4243 16.1437 21.751 17.1895" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <!-- Notification Dropdown -->
                    <NotificationDropdown />

                    <!-- User Profile Dropdown -->
                    <div class="relative" ref="dropdownRef">
                        <button
                            type="button"
                            class="flex items-center gap-2 p-1 rounded-full bg-[#f4f6f8] dark:bg-[#1a2941] border border-[#e0e6ed] dark:border-[#1b2e4b] hover:border-primary/50 transition shadow-xs"
                            @click="dropdownOpen = !dropdownOpen"
                        >
                            <img
                                class="h-8 w-8 rounded-full object-cover ring-2 ring-primary/20"
                                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&rounded=true&background=4361ee&color=fff`"
                                alt="User Avatar"
                            />
                            <span class="hidden md:inline-block font-bold text-xs text-[#0e1726] dark:text-white max-w-[120px] truncate pr-1">
                                {{ user?.name }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-[#506690] dark:text-[#888ea8] mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="dropdownOpen"
                            class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white dark:bg-[#1b2e4b] ring-1 ring-black ring-opacity-5 py-2 z-50 animate__animated animate__fadeIn"
                        >
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                                <img
                                    class="h-10 w-10 rounded-md object-cover shrink-0"
                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&rounded=true&background=4361ee&color=fff`"
                                    alt="User Avatar"
                                />
                                <div class="truncate">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ user?.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ user?.email }}
                                    </p>
                                    <span
                                        v-if="userRole"
                                        class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary uppercase tracking-wide"
                                    >
                                        {{ userRole }}
                                    </span>
                                </div>
                            </div>

                            <div class="py-1">
                                <button
                                    type="button"
                                    @click="logout"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 font-medium cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import CurrencyTicker from '@/Components/CurrencyTicker.vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';

defineProps({
    currencies: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['toggle-sidebar']);

const page = usePage();
const user = computed(() => page.props.auth?.user);
const userRole = computed(() => user.value?.roles?.[0] || 'User');
const userInitials = computed(() => {
    if (!user.value?.name) return 'U';
    return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

const isDark = ref(false);
const dropdownOpen = ref(false);
const dropdownRef = ref(null);

function toggleTheme() {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
}

function logout() {
    router.post('/logout');
}

function handleClickOutside(event) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        dropdownOpen.value = false;
    }
}

onMounted(() => {
    isDark.value = localStorage.getItem('theme') === 'dark' || document.documentElement.classList.contains('dark');
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
