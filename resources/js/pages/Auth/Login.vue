<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LoadingAnimated from '@/components/loading-animated';
import AuthBackgroundShapes from '@/components/ui/auth-background-shapes';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
  DialogTrigger,
} from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { ChevronLeft, Info, Key, Mail, HelpCircle } from 'lucide-vue-next';
import { userTypes } from '@/constants/auth';
import type { DocumentTypeOption, UserType } from '@/types/auth';

interface LoginProps {
  Coddoc?: Record<string, string>;
  Tipsoc?: Record<string, string>;
  Codciu?: Record<string, string>;
  Detadoc?: Record<string, string>;
  errors?: Record<string, string>;
}

const props = defineProps<LoginProps>();

const page = usePage<{ name?: string; quote?: { message: string; author: string } }>();

// State
const selectedUserType = ref<UserType | null>(null);
const documentType = ref('');
const identification = ref('');
const password = ref('');
const processing = ref(false);
const coddoc = ref<Record<string, string>>({});
const dialog = ref<{ message: string; type: 'success' | 'error' } | null>(null);

// Computed
const documentTypeOptions = computed<DocumentTypeOption[]>(() =>
  Object.entries(coddoc.value || {}).map(([value, label]) => ({ value, label })),
);

const tipoValue = computed(() => {
  const map: Record<string, string> = {
    empresa: 'E',
    independiente: 'I',
    facultativo: 'F',
    particular: 'P',
    trabajador: 'T',
    pensionado: 'O',
  };
  return selectedUserType.value ? map[selectedUserType.value] ?? '' : '';
});

const selectedUserTypeLabel = computed(() => {
  return userTypes.find((ut) => ut.id === selectedUserType.value)?.label ?? '';
});

// Methods
function handleUserTypeSelect(userType: UserType) {
  selectedUserType.value = userType;
}

function handleBack() {
  selectedUserType.value = null;
  documentType.value = '';
  identification.value = '';
  password.value = '';
  dialog.value = null;
}

async function handleLogin(e: Event) {
  e.preventDefault();
  processing.value = true;
  dialog.value = null;

  try {
    const response = await fetch(route('login.authenticate'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
      },
      body: JSON.stringify({
        documentType: documentType.value,
        password: password.value,
        identification: identification.value ? parseInt(identification.value) : null,
        tipo: tipoValue.value,
      }),
    });

    const responseJson = await response.json();

    if (response.ok) {
      // Redirect on success via Inertia
      window.location.href = responseJson.redirect || '/';
    } else {
      dialog.value = {
        message: responseJson.message || 'No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.',
        type: 'error',
      };
    }
  } catch (error) {
    dialog.value = {
      message: 'No fue posible iniciar sesión. Verifique su conexión e intente nuevamente.',
      type: 'error',
    };
  } finally {
    processing.value = false;
  }
}

async function loadParams() {
  try {
    const response = await fetch(route('login.params'), {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    });
    const responseJson = await response.json();
    if (response.ok) {
      coddoc.value = responseJson?.Coddoc ?? {};
    }
  } catch (error) {
    dialog.value = { message: 'No fue posible cargar los parámetros de login.', type: 'error' };
  }
}

onMounted(() => {
  if (props.errors?.message) {
    dialog.value = { message: props.errors.message, type: 'error' };
  }
  loadParams();
});
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

          <h1 class="text-4xl font-bold mb-2">BIENVENIDO</h1>
          <div class="w-16 h-0.5 bg-white mb-6" />
          <p class="text-emerald-100 text-lg mb-6">Comfaca En Línea</p>

          <div class="text-emerald-100 text-sm leading-relaxed mb-6 space-y-3">
            <p>Bienvenido a Comfaca En Línea, el portal virtual de la Caja de Compensación Familiar del Caquetá – COMFACA.</p>
            <p>Mediante esta plataforma podrá reportar novedades, consultar la información de sus afiliados, realizar trámites administrativos y acceder a los servicios institucionales.</p>
          </div>

          <Link
            :href="route('register')"
            class="inline-flex items-center text-emerald-200 hover:text-white transition-colors text-sm mb-4"
          >
            <ChevronLeft class="w-4 h-4 mr-1" />
            Crear cuenta
          </Link>

          <Dialog>
            <DialogTrigger as-child>
              <Button
                variant="outline"
                class="border-white/30 text-white bg-white/10 hover:bg-white/20 hover:text-white mt-2 w-fit"
              >
                <Info class="w-4 h-4 mr-2" />
                Ver opciones de ingreso
              </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[600px]">
              <DialogHeader>
                <DialogTitle>Opciones de ingreso</DialogTitle>
              </DialogHeader>
              <div class="grid gap-3">
                <div
                  v-for="ut in userTypes"
                  :key="ut.id"
                  class="flex items-center gap-3"
                >
                  <component :is="ut.icon" class="w-5 h-5 shrink-0" />
                  <p class="font-medium">{{ ut.label }}</p>
                </div>
              </div>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      <!-- Right Panel - Login Form -->
      <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
        <div class="w-full max-w-md mx-auto">
          <!-- Mobile Header -->
          <div class="flex lg:hidden items-center gap-3 mb-8">
            <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
            <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
          </div>

          <!-- User Type Selection -->
          <div v-if="!selectedUserType" class="flex flex-col">
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Iniciar sesión</h1>
            <p class="text-sm text-gray-500 mb-6">Comfaca en línea</p>

            <div class="grid grid-cols-2 gap-3 mb-6">
              <button
                v-for="ut in userTypes"
                :key="ut.id"
                type="button"
                class="flex flex-col items-center p-4 rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors text-center"
                @click="handleUserTypeSelect(ut.id as UserType)"
              >
                <component :is="ut.icon" class="mb-2 w-6 h-6 text-gray-600" />
                <span class="text-xs font-medium text-gray-700">{{ ut.label }}</span>
              </button>
            </div>

            <div class="flex flex-col items-center gap-3 text-sm">
              <Link :href="route('password.request')" class="text-gray-500 hover:text-emerald-600 flex items-center">
                <HelpCircle class="mr-1 w-4 h-4" />
                Olvidé mi clave
              </Link>
              <Link :href="route('register')" class="text-gray-500 hover:text-emerald-600 flex items-center">
                <Key class="mr-1 w-4 h-4" />
                Crear cuenta
              </Link>
              <Link
                :href="route('web.noty_cambio_correo')"
                class="text-gray-500 hover:text-emerald-600 flex items-center"
              >
                <Mail class="mr-1 w-4 h-4" />
                Solicitar cambio de correo
              </Link>
            </div>
          </div>

          <!-- Login Form -->
          <div v-else>
            <button
              type="button"
              class="flex items-center text-gray-500 hover:text-emerald-600 transition-colors mb-6"
              @click="handleBack"
            >
              <ChevronLeft class="w-5 h-5" />
              <span class="ml-1">Volver</span>
            </button>

            <h1 class="text-xl font-semibold text-gray-900 mb-6">{{ selectedUserTypeLabel }}</h1>

            <form @submit="handleLogin" class="space-y-4">
              <div>
                <Label for="documentType" class="text-sm font-medium text-gray-700">
                  Tipo de documento
                </Label>
                <Select v-model="documentType" class="mt-1 w-full">
                  <option value="" disabled>Selecciona el tipo de documento</option>
                  <option
                    v-for="doc in documentTypeOptions"
                    :key="doc.value"
                    :value="doc.value"
                  >
                    {{ doc.label }}
                  </option>
                </Select>
              </div>

              <div>
                <Label for="identification" class="text-sm font-medium text-gray-700">
                  Número de identificación
                </Label>
                <Input
                  id="identification"
                  v-model="identification"
                  type="number"
                  placeholder="Ingresa tu número de identificación"
                  class="mt-1 w-full"
                  required
                />
              </div>

              <div>
                <Label for="password" class="text-sm font-medium text-gray-700">
                  Clave
                </Label>
                <Input
                  id="password"
                  v-model="password"
                  type="password"
                  placeholder="Ingresa tu clave"
                  class="mt-1 w-full"
                  required
                />
              </div>

              <div class="pt-2">
                <Button
                  type="submit"
                  :disabled="!documentType || !identification || !password || processing"
                  class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white py-3 rounded-lg font-medium shadow-lg"
                >
                  {{ processing ? 'Iniciando sesión...' : 'Iniciar sesión' }}
                </Button>
              </div>

              <div class="flex justify-center text-sm">
                <Link :href="route('password.request')" class="text-gray-500 hover:text-emerald-600 flex items-center">
                  <HelpCircle class="mr-1 w-4 h-4" />
                  Olvidé mi clave
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <LoadingAnimated :show="processing" />

    <!-- Dialog -->
    <Dialog :open="dialog !== null" @update:open="(open) => !open && (dialog = null)">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle :class="dialog?.type === 'success' ? 'text-emerald-600' : 'text-red-600'">
            {{ dialog?.type === 'success' ? 'Éxito' : 'Error de Autenticación' }}
          </DialogTitle>
        </DialogHeader>
        <div class="py-4">
          <p class="text-sm text-gray-700 whitespace-pre-line">{{ dialog?.message }}</p>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="dialog = null">Cerrar</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
