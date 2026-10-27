<template>
    <div class="inline-block text-left">
        <button
            ref="buttonRef"
            type="button"
            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[#506690] dark:text-[#888ea8] hover:text-primary hover:bg-[#ebedf2] dark:hover:bg-[#1a2941] focus:outline-none transition active:scale-95"
            @click.stop="toggle"
            title="Actions"
            aria-label="Actions"
            :aria-expanded="isOpen"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="5" r="1.75" fill="currentColor" />
                <circle cx="12" cy="12" r="1.75" fill="currentColor" />
                <circle cx="12" cy="19" r="1.75" fill="currentColor" />
            </svg>
        </button>

        <!-- Teleported to body so options are always ABOVE table boundaries, not clipped by overflow-x: auto -->
        <Teleport to="body">
            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <div
                    v-if="isOpen"
                    ref="floatingRef"
                    :style="floatingStyles"
                    class="fixed z-[9999] w-48 rounded-lg bg-white dark:bg-[#1b2e4b] shadow-2xl border border-[#e0e6ed] dark:border-[#191e3a] py-1.5 focus:outline-none"
                    @click="closeOnAction ? close() : null"
                >
                    <slot :close="close" />
                </div>
            </transition>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, nextTick, onMounted, onUnmounted } from 'vue'
import { computePosition, offset, flip, shift, autoUpdate } from '@floating-ui/dom'

const props = defineProps({
    closeOnAction: {
        type: Boolean,
        default: true,
    },
    placement: {
        type: String,
        default: 'bottom-end',
    },
})

const isOpen = ref(false)
const buttonRef = ref(null)
const floatingRef = ref(null)

const floatingStyles = reactive({
    top: '0px',
    left: '0px',
})

let cleanupAutoUpdate = null

function updatePosition() {
    if (!buttonRef.value || !floatingRef.value) return

    computePosition(buttonRef.value, floatingRef.value, {
        placement: props.placement,
        middleware: [
            offset(6),
            flip({ fallbackPlacements: ['top-end', 'bottom-start', 'top-start'] }),
            shift({ padding: 8 }),
        ],
    }).then(({ x, y }) => {
        floatingStyles.left = `${x}px`
        floatingStyles.top = `${y}px`
    })
}

async function toggle() {
    if (isOpen.value) {
        close()
    } else {
        isOpen.value = true
        await nextTick()
        if (buttonRef.value && floatingRef.value) {
            updatePosition()
            cleanupAutoUpdate = autoUpdate(buttonRef.value, floatingRef.value, updatePosition)
        }
    }
}

function close() {
    isOpen.value = false
    if (cleanupAutoUpdate) {
        cleanupAutoUpdate()
        cleanupAutoUpdate = null
    }
}

function handleClickOutside(event) {
    if (!isOpen.value) return
    const clickedButton = buttonRef.value && buttonRef.value.contains(event.target)
    const clickedMenu = floatingRef.value && floatingRef.value.contains(event.target)
    if (!clickedButton && !clickedMenu) {
        close()
    }
}

function handleKeydown(event) {
    if (event.key === 'Escape' && isOpen.value) {
        close()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    document.removeEventListener('keydown', handleKeydown)
    if (cleanupAutoUpdate) {
        cleanupAutoUpdate()
    }
})

defineExpose({ close })
</script>
