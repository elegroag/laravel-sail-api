import { ref, onMounted, onUnmounted } from 'vue';

const MOBILE_BREAKPOINT = 768;

export function useIsMobile() {
    const isMobile = ref(false);

    const updateMobile = () => {
        isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;
    };

    onMounted(() => {
        updateMobile();
        const mql = window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT - 1}px)`);
        mql.addEventListener('change', updateMobile);
        onUnmounted(() => mql.removeEventListener('change', updateMobile));
    });

    return { isMobile };
}