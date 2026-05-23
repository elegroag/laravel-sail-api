<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LoadingAnimated from '@/components/loading-animated';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { userTypes } from '@/constants/auth';
import type { DocumentTypeOption, UserType } from '@/types/auth';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronLeft, HelpCircle, Info, Key, Mail } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

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
const documentTypeOptions = computed<DocumentTypeOption[]>(() => Object.entries(coddoc.value || {}).map(([value, label]) => ({ value, label })));

const tipoValue = computed(() => {
    const map: Record<string, string> = {
        empresa: 'E',
        independiente: 'I',
        facultativo: 'F',
        particular: 'P',
        trabajador: 'T',
        pensionado: 'O',
    };
    return selectedUserType.value ? (map[selectedUserType.value] ?? '') : '';
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

    router.post(
        route('login.authenticate'),
        {
            documentType: documentType.value,
            password: password.value,
            identification: identification.value ? parseInt(identification.value) : null,
            tipo: tipoValue.value,
        },
        {
            onSuccess(page) {
                if (page.props.redirect) {
                    window.location.href = page.props.redirect as string;
                } else {
                    window.location.href = '/';
                }
            },
            onError(errors) {
                const message = Object.values(errors).find((msg) => typeof msg === 'string') as string | undefined;
                dialog.value = {
                    message: message || 'No fue posible iniciar sesión. Verifique sus datos e intente nuevamente.',
                    type: 'error',
                };
            },
            onFinish() {
                processing.value = false;
            },
        },
    );
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
  } catch {
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
        <div class="flex min-h-dvh flex-col lg:relative lg:flex-row">
            <!-- Left Panel - Welcome Section (Desktop only) -->
            <div class="hidden lg:relative lg:block lg:min-h-dvh lg:w-1/2 lg:overflow-hidden">
                <!-- Absolute background layer -->
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700" />

                <!-- Absolute decorative shapes -->
                <div
                    class="pointer-events-none absolute top-0 right-0 h-40 w-40 translate-x-1/4 -translate-y-1/2 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 opacity-60"
                />
                <div
                    class="pointer-events-none absolute bottom-0 left-0 h-32 w-32 -translate-x-1/4 -translate-y-1/3 rounded-full bg-gradient-to-tr from-emerald-800 to-emerald-600 opacity-40"
                />
                <div
                    class="pointer-events-none absolute top-1/2 left-0 h-20 w-20 -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-r from-teal-500 to-emerald-500 opacity-30"
                />

                <!-- Content layer -->
                <div class="relative z-10 mx-auto flex h-full max-w-md flex-col items-center justify-center p-5 text-white">
                    <Link :href="route('login')" class="mb-8 flex items-center justify-center text-lg font-medium">
                        <AppLogoIcon class="fill-current text-white" />
                    </Link>

                    <h1 class="mb-4 text-4xl font-bold">BIENVENIDO</h1>
                    <div class="mb-6 h-0.5 w-16 bg-white" />
                    <p class="mb-6 text-lg text-emerald-100">Comfaca En Línea</p>

                    <div class="mb-6 space-y-3 text-sm leading-relaxed text-emerald-100">
                        <p>Bienvenido a Comfaca En Línea, el portal virtual de la Caja de Compensación Familiar del Caquetá – COMFACA.</p>
                        <p>
                            Mediante esta plataforma podrá reportar novedades, consultar la información de sus afiliados, realizar trámites
                            administrativos y acceder a los servicios institucionales.
                        </p>
                    </div>

                    <Link :href="route('register')" class="mb-4 inline-flex items-center text-sm text-emerald-200 transition-colors hover:text-white">
                        <ChevronLeft class="mr-1 h-4 w-4" />
                        Crear cuenta
                    </Link>

                    <Dialog>
                        <DialogTrigger as-child>
                            <Button variant="outline" class="mt-2 w-fit border-white/30 bg-white/10 text-white hover:bg-white/20 hover:text-white">
                                <Info class="mr-2 h-4 w-4" />
                                Ver opciones de ingreso
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[600px]">
                            <DialogHeader>
                                <DialogTitle>Opciones de ingreso</DialogTitle>
                            </DialogHeader>
                            <div class="grid gap-3">
                                <div v-for="ut in userTypes" :key="ut.id" class="flex items-center gap-3">
                                    <component :is="ut.icon" class="h-5 w-5 shrink-0" />
                                    <p class="font-medium">{{ ut.label }}</p>
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="flex flex-1 flex-col justify-center px-6 py-10 lg:w-1/2 lg:px-16">
                <div class="mx-auto w-full max-w-md">
                    <!-- Mobile Header -->
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <img src="/img/comfaca-logo.png" alt="COMFACA" class="h-10 w-auto" />
                        <span class="text-lg font-bold text-gray-900">COMFACA EN LÍNEA</span>
                    </div>

                    <!-- User Type Selection -->
                    <div v-if="!selectedUserType" class="flex flex-col">
                        <h1 class="mb-1 text-2xl font-semibold text-gray-900">Iniciar sesión</h1>
                        <p class="mb-6 text-sm text-gray-500">Comfaca en línea</p>

                        <div class="mb-6 grid grid-cols-2 gap-3">
                            <button
                                v-for="ut in userTypes"
                                :key="ut.id"
                                type="button"
                                class="flex flex-col items-center rounded-lg border border-gray-200 p-4 text-center transition-colors hover:border-emerald-500 hover:bg-emerald-50"
                                @click="handleUserTypeSelect(ut.id as UserType)"
                            >
                                <component :is="ut.icon" class="mb-2 h-6 w-6 text-gray-600" />
                                <span class="text-xs font-medium text-gray-700">{{ ut.label }}</span>
                            </button>
                        </div>

                        <div class="flex flex-col items-center gap-3 text-sm">
                            <Link :href="route('password.request')" class="flex items-center text-gray-500 hover:text-emerald-600">
                                <HelpCircle class="mr-1 h-4 w-4" />
                                Olvidé mi clave
                            </Link>
                            <Link :href="route('register')" class="flex items-center text-gray-500 hover:text-emerald-600">
                                <Key class="mr-1 h-4 w-4" />
                                Crear cuenta
                            </Link>
                            <Link :href="route('web.noty_cambio_correo')" class="flex items-center text-gray-500 hover:text-emerald-600">
                                <Mail class="mr-1 h-4 w-4" />
                                Solicitar cambio de correo
                            </Link>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <div v-else>
                        <button
                            type="button"
                            class="mb-6 flex items-center text-gray-500 transition-colors hover:text-emerald-600"
                            @click="handleBack"
                        >
                            <ChevronLeft class="h-5 w-5" />
                            <span class="ml-1">Volver</span>
                        </button>

                        <h1 class="mb-6 text-xl font-semibold text-gray-900">{{ selectedUserTypeLabel }}</h1>

                        <form @submit="handleLogin" class="space-y-4">
                            <div>
                                <Label for="documentType" class="text-sm font-medium text-gray-700"> Tipo de documento </Label>
                                <Select v-model="documentType" class="mt-1 w-full">
                                    <option value="" disabled>Selecciona el tipo de documento</option>
                                    <option v-for="doc in documentTypeOptions" :key="doc.value" :value="doc.value">
                                        {{ doc.label }}
                                    </option>
                                </Select>
                            </div>

                            <div>
                                <Label for="identification" class="text-sm font-medium text-gray-700"> Número de identificación </Label>
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
                                <Label for="password" class="text-sm font-medium text-gray-700"> Clave </Label>
                                <Input id="password" v-model="password" type="password" placeholder="Ingresa tu clave" class="mt-1 w-full" required />
                            </div>

                            <div class="pt-2">
                                <Button
                                    type="submit"
                                    :disabled="!documentType || !identification || !password || processing"
                                    class="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 py-3 font-medium text-white shadow-lg hover:from-emerald-700 hover:to-teal-700"
                                >
                                    {{ processing ? 'Iniciando sesión...' : 'Iniciar sesión' }}
                                </Button>
                            </div>

                            <div class="flex justify-center text-sm">
                                <Link :href="route('password.request')" class="flex items-center text-gray-500 hover:text-emerald-600">
                                    <HelpCircle class="mr-1 h-4 w-4" />
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
                    <p class="text-sm whitespace-pre-line text-gray-700">{{ dialog?.message }}</p>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="dialog = null">Cerrar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
