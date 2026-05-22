<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
// import AppLogoIcon from '@/components/AppLogoIcon.vue';

const props = defineProps<{
    status: number;
}>();

const errors: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Forbidden',
        description: "You don't have permission to access this page.",
    },
    404: {
        title: 'Not Found',
        description: "The page you're looking for doesn't exist.",
    },
    419: {
        title: 'Page Expired',
        description: 'Your session has expired. Please refresh and try again.',
    },
    429: {
        title: 'Too Many Requests',
        description:
            "You've made too many requests. Please wait a moment and try again.",
    },
    500: {
        title: 'Server Error',
        description: 'Something went wrong on our end. Please try again later.',
    },
    503: {
        title: 'Service Unavailable',
        description:
            "We're currently performing maintenance. Please check back soon.",
    },
};

const goBack = () => window.history.back();

const error = computed(
    () =>
        errors[props.status] ?? {
            title: 'Error',
            description: 'An unexpected error occurred.',
        },
);
</script>

<template>
    <div
        class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10"
    >
        <Head :title="`${status} - ${error.title}`" />

        <div class="flex flex-col items-center gap-6 text-center">
            <Link href="/" class="flex flex-col items-center gap-2 font-medium">
                <div
                    class="bg-primary mb-1 flex h-9 w-9 items-center justify-center rounded-md"
                >
                    <span class="text-lg font-bold text-white">CL</span>
                </div>
            </Link>

            <div class="space-y-2">
                <p class="text-foreground text-7xl font-bold">{{ status }}</p>
                <h1 class="text-foreground text-xl font-medium">
                    {{ error.title }}
                </h1>
                <p class="text-muted-foreground text-sm">
                    {{ error.description }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    @click="goBack"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-xs transition-colors"
                >
                    Go Back
                </button>
                <Link
                    href="/"
                    class="border-input bg-background text-foreground hover:bg-accent hover:text-accent-foreground inline-flex items-center justify-center rounded-md border px-4 py-2 text-sm font-medium shadow-xs transition-colors"
                >
                    Go Home
                </Link>
            </div>
        </div>
    </div>
</template>
