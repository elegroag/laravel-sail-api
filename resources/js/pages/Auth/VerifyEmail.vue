<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { ChevronLeft, Mail, MessageCircle, Check } from 'lucide-vue-next'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { useVerifyController } from '@/composables/useVerifyController'

type VerifyEmailProps = {
    documento?: string
    coddoc?: string
    tipo?: string
    errors?: Record<string, string>
    status?: string
    option_request?: string
    error?: string
}

const props = defineProps<VerifyEmailProps>()

const {
    state,
    formattedCountdown,
    deliveryChannelLabel,
    handleDeliveryMethodChange,
    handleInputChange,
    handlePaste,
    handleVerify,
    handleResendCode,
    processing,
    dialog,
    setDialog
} = useVerifyController({
    documento: props.documento,
    coddoc: props.coddoc,
    tipo: props.tipo,
    errors: props.errors ?? {},
    error: props.error,
    option_request: props.option_request
})

const inputRefs = ref<(HTMLInputElement | null)[]>([])

const DeliveryOptions = [
    { id: 'email', label: 'Correo electrónico', description: 'Recibirás el código en tu bandeja de entrada.', icon: Mail },
    { id: 'whatsapp', label: 'WhatsApp', description: 'Enviaremos el código a tu número registrado.', icon: MessageCircle }
] as const
</script>

<template>
  <div class="min-h-dvh w-full bg-white">
    <div class="flex min-h-dvh flex-col lg:flex-row lg:relative">
      <!-- Left Panel - Welcome Section (Desktop only) -->
      <div class="hidden lg:block lg:w-1/2 lg:min-h-dvh lg:relative lg:overflow-hidden">
        <!-- Absolute background layer -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700" />

        <!-- Absolute decorative shapes -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full opacity-60 -translate-y-1/2 translate-x-1/4 pointer-events-none" />
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-emerald-800 to-emerald-600 rounded-full opacity-40 -translate-y-1/3 -translate-x-1/4 pointer-events-none" />
        <div class="absolute top-1/2 left-0 w-20 h-20 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full opacity-30 -translate-y-1/2 -translate-x-1/2 pointer-events-none" />

        <!-- Content layer -->
        <div class="relative z-10 flex flex-col justify-center h-full p-10 text-white">
          <Link href="/" class="flex items-center text-lg font-medium mb-8">
            <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
            COMFACA
          </Link>

          <h1 class="text-4xl font-bold mb-2">VERIFICACIÓN DE CORREO</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>Te enviamos un código de 4 dígitos para asegurar que eres el propietario del correo registrado.</p>
          </div>

          <Link
            :href="route('login')"
            class="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm mb-4"
          >
            <ChevronLeft class="w-4 h-4 mr-1" />
            ¿Ya tienes cuenta? Inicia sesión
          </Link>
        </div>
      </div>

      <!-- Right Panel - Verify Form -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

          <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-emerald-600">
            Reenviamos un nuevo código de verificación a tu correo electrónico.
          </div>

          <div v-if="state.isVerified" class="text-center space-y-6">
            <div class="flex justify-center">
              <div class="rounded-full bg-emerald-100 p-4">
                <Check class="h-16 w-16 text-emerald-600" />
              </div>
            </div>
            <h1 class="text-3xl font-semibold">¡Email verificado!</h1>
            <p class="text-muted-foreground max-w-md">Tu correo ha sido confirmado exitosamente. Ahora puedes iniciar sesión.</p>
            <button
              @click="router.visit(route('login'))"
              class="px-8 py-2 text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700"
            >
              Ir al inicio de sesión
            </button>
          </div>

          <form v-else @submit="handleVerify" class="space-y-6">
            <div class="text-center space-y-2">
              <component
                :is="state.deliveryMethod === 'email' ? Mail : MessageCircle"
                class="h-12 w-12 text-emerald-600 mx-auto"
              />
              <p class="text-sm text-muted-foreground">
                {{ state.deliveryMethod === 'email'
                  ? 'Ingresa el código de verificación que enviamos a tu correo electrónico.'
                  : 'Ingresa el código de verificación que enviamos a tu WhatsApp.'
                }}
              </p>
            </div>

            <div class="space-y-3">
              <p class="text-sm font-medium text-center text-muted-foreground">
                Selecciona dónde deseas recibir el código ({{ deliveryChannelLabel }}).
              </p>
              <div class="grid gap-3 sm:grid-cols-2">
                <button
                  v-for="opt in DeliveryOptions"
                  :key="opt.id"
                  type="button"
                  @click="handleDeliveryMethodChange(opt.id)"
                  :class="[
                    'rounded-lg border px-4 py-3 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500',
                    state.deliveryMethod === opt.id
                      ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm'
                      : 'border-muted'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <span :class="[
                      'inline-flex items-center justify-center rounded-full border p-2',
                      state.deliveryMethod === opt.id
                        ? 'border-emerald-400 bg-white text-emerald-600'
                        : 'border-muted text-muted-foreground'
                    ]">
                      <component :is="opt.icon" class="h-5 w-5" />
                    </span>
                    <div>
                      <p class="text-sm font-semibold">{{ opt.label }}</p>
                      <p class="text-xs text-muted-foreground">{{ opt.description }}</p>
                    </div>
                  </div>
                </button>
              </div>
            </div>

            <div class="flex justify-center gap-3">
              <input
                v-for="(digit, index) in state.code"
                :key="`digit-${index}`"
                type="text"
                inputmode="numeric"
                maxlength="1"
                :ref="(el) => { if (el) inputRefs[index] = el as HTMLInputElement }"
                :value="digit"
                @input="handleInputChange(index, ($event.target as HTMLInputElement).value)"
                @keydown="(e: KeyboardEvent) => {
                  if (e.key === 'Backspace' && !digit && index > 0) {
                    inputRefs[index - 1]?.focus()
                  }
                }"
                @paste="handlePaste"
                class="w-14 h-14 text-center text-xl font-semibold text-gray-700 border border-gray-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="0"
              />
            </div>

            <div class="space-y-4">
              <button
                type="submit"
                :disabled="processing"
                class="w-full flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
              >
                {{ processing ? 'Validando código...' : 'Verificar código' }}
              </button>

              <div class="text-center">
                <button
                  v-if="state.canResend"
                  type="button"
                  @click="handleResendCode"
                  :disabled="processing"
                  class="text-sm text-emerald-600 hover:text-emerald-700 font-medium underline-offset-2 hover:underline disabled:opacity-50"
                >
                  {{ processing ? 'Reenviando...' : '¿No recibiste el código? Reenviar código' }}
                </button>
                <p v-else class="text-sm text-muted-foreground">
                  Podrás reenviar el código en <span class="font-medium">{{ formattedCountdown }}</span>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Dialog -->
      <div v-if="dialog" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="setDialog(null)" />
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
          <div class="mb-4">
            <h3 :class="['text-lg font-medium', dialog.type === 'success' ? 'text-emerald-600' : 'text-red-600']">
              {{ dialog.type === 'success' ? 'Verificación Exitosa' : 'Error de Verificación' }}
            </h3>
          </div>
          <div class="mb-4">
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ dialog.message }}</p>
          </div>
          <div class="flex justify-end">
            <button
              @click="setDialog(null)"
              class="px-4 py-2 text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>