<template>
    <div
        v-if="impersonator"
        class="bg-gradient-to-r from-red-600 via-rose-600 to-amber-600 text-white px-4 py-2 text-sm flex items-center justify-between shadow-md sticky top-0 z-50 animate__animated animate__fadeInDown"
    >
        <div class="flex items-center space-x-2 font-medium">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>
                You are currently impersonating <strong>{{ user?.name }}</strong> ({{ user?.email }}).
            </span>
        </div>
        <a
            href="/dashboard/impersonate/leave"
            class="bg-white text-red-700 hover:bg-red-50 px-3 py-1 rounded text-xs font-bold transition shadow-sm inline-flex items-center space-x-1"
        >
            <span>Leave Impersonation</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </a>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const impersonator = computed(() => page.props.auth?.impersonator);
const user = computed(() => page.props.auth?.user);
</script>
