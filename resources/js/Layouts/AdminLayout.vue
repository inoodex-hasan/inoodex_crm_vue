<template>
    <div class="main-section antialiased relative font-nunito text-sm font-normal">
        <!-- Persistent Impersonation Safety Banner -->
        <ImpersonationBanner />

        <div class="main-container min-h-screen text-black dark:text-white-dark">
            <!-- Mobile Backdrop Overlay -->
            <transition name="fade">
                <div
                    v-if="mobileSidebarOpen"
                    class="fixed inset-0 bg-black/50 backdrop-blur-xs z-40 lg:hidden"
                    @click="mobileSidebarOpen = false"
                />
            </transition>

            <!-- Sidebar Navigation -->
            <Sidebar
                :collapsed="desktopSidebarCollapsed"
                :mobile-open="mobileSidebarOpen"
                @close-sidebar="mobileSidebarOpen = false"
            />

            <!-- Main Content Area -->
            <div
                class="main-content flex min-h-screen flex-col transition-all duration-300"
                :class="{ 'lg:ltr:ml-[260px] lg:rtl:mr-[260px]': !desktopSidebarCollapsed }"
            >
                <!-- Top Header -->
                <Header
                    :currencies="currencies"
                    @toggle-sidebar="handleToggleSidebar"
                />

                <!-- Page Content Body -->
                <main class="p-4 sm:p-6 flex-1 bg-[#fafafa] dark:bg-[#060818]">
                    <!-- Flash Messages -->
                    <div v-if="flash?.success && showSuccess" class="mb-4 flex items-center justify-between p-3.5 rounded text-white bg-success shadow-sm animate__animated animate__fadeIn">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold">✓</span>
                            <span>{{ flash.success }}</span>
                        </div>
                        <button type="button" @click="showSuccess = false" class="hover:opacity-80">✕</button>
                    </div>

                    <div v-if="flash?.error && showError" class="mb-4 flex items-center justify-between p-3.5 rounded text-white bg-danger shadow-sm animate__animated animate__fadeIn">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold">✕</span>
                            <span>{{ flash.error }}</span>
                        </div>
                        <button type="button" @click="showError = false" class="hover:opacity-80">✕</button>
                    </div>

                    <div v-if="flash?.warning && showWarning" class="mb-4 flex items-center justify-between p-3.5 rounded text-white bg-warning shadow-sm animate__animated animate__fadeIn">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold">⚠</span>
                            <span>{{ flash.warning }}</span>
                        </div>
                        <button type="button" @click="showWarning = false" class="hover:opacity-80">✕</button>
                    </div>

                    <!-- Injected Page Content -->
                    <slot />
                </main>

                <!-- Footer -->
                <footer class="p-4 text-center font-semibold text-xs text-gray-500 bg-white dark:bg-[#0e1726] border-t border-gray-100 dark:border-gray-800">
                    © {{ new Date().getFullYear() }} Inoodex CRM. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue';
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';

defineProps({
    currencies: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const flash = computed(() => page.props.flash);

const desktopSidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);

const showSuccess = ref(true);
const showError = ref(true);
const showWarning = ref(true);

function handleToggleSidebar() {
    if (window.innerWidth < 1024) {
        mobileSidebarOpen.value = !mobileSidebarOpen.value;
    } else {
        desktopSidebarCollapsed.value = !desktopSidebarCollapsed.value;
    }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
