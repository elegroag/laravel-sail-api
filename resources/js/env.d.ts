import 'vue'
import type { PageProps as InertiaPageProps } from '@inertiajs/vue3'

declare module '@inertiajs/vue3' {
    interface PageProps extends InertiaPageProps {
        auth: {
            user: {
                id: number
                name: string
                email: string
                avatar?: string
                email_verified_at: string | null
                created_at: string
                updated_at: string
                [key: string]: unknown
            }
        }
        sidebarOpen: boolean
        ziggy: any
        flash?: {
            success?: string
            error?: string
            [key: string]: unknown
        }
        [key: string]: unknown
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: (name: string, params?: Record<string, any>) => string
    }
}