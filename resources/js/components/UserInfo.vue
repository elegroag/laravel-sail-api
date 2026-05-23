<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user: User | null;
    showEmail?: boolean;
};

withDefaults(defineProps<Props>(), {
    showEmail: false,
});
</script>

<template>
    <Avatar v-if="user" class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.nombre ?? '') }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user?.nombre ?? 'Usuario' }}</span>
        <span v-if="showEmail && user" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
