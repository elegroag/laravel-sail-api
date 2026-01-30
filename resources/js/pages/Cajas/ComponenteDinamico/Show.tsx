import AppLayout from '@/layouts/AppLayoutTemplate';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import type { Componente as ComponenteType, DataSourceItem, Formulario as FormularioType } from '@/types/cajas';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from '@/components/ui/dialog';

type Props = {
    componente: (ComponenteType & {
        data_source: DataSourceItem[] | null;
        event_config: Record<string, unknown>;
        formulario?: Pick<FormularioType, 'id' | 'name' | 'title'> | null;
        created_at: string;
        updated_at: string;
    });
};

export default function Show({ componente }: Props) {
    const [deleting, setDeleting] = useState(false);
    const [confirmOpen, setConfirmOpen] = useState(false);

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const handleDelete = () => {
        setDeleting(true);
        router.delete(`/cajas/componente-dinamico/${componente.id}`, {
            onSuccess: () => {
                router.visit('/cajas/componente-dinamico');
            },
            onError: () => {
                setDeleting(false);
                setConfirmOpen(false);
            }
        });
    };

    const confirmDelete = () => {
        handleDelete();
    };

    return (
        <AppLayout title={`Componente: ${componente.label}`}>
            <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900">{componente.label}</h1>
                            <p className="mt-1 text-sm text-gray-500">
                                Componente dinámico del formulario {componente.formulario?.title || 'Sin asignar'}
                            </p>
                        </div>
                        <div className="flex space-x-3">
                            <Link
                                href="/cajas/componente-dinamico"
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Volver
                            </Link>
                            <Link
                                href={`/cajas/componente-dinamico/${componente.id}/edit`}
                                className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Editar
                            </Link>
                            <button
                                onClick={() => setConfirmOpen(true)}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                {/* Main Content */}
                <div className="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div className="px-4 py-5 sm:px-6">
                        <h3 className="text-lg leading-6 font-medium text-gray-900">
                            Información del Componente
                        </h3>
                        <p className="mt-1 max-w-2xl text-sm text-gray-500">
                            Detalles completos del componente dinámico.
                        </p>
                    </div>
                    <div className="border-t border-gray-200">
                        <dl>
                            {/* Basic Information */}
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Nombre</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.name}
                                </dd>
                            </div>

                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Etiqueta</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.label}
                                </dd>
                            </div>

                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Tipo</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {componente.type}
                                    </span>
                                </dd>
                            </div>

                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Tipo de Formulario</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {componente.form_type}
                                    </span>
                                </dd>
                            </div>

                            {/* Layout Configuration */}
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Grupo</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.group_id}
                                </dd>
                            </div>

                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Orden</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.order}
                                </dd>
                            </div>

                            {/* State */}
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Estado</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div className="flex space-x-2">
                                        {componente.is_disabled && (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Deshabilitado
                                            </span>
                                        )}
                                        {componente.is_readonly && (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Solo Lectura
                                            </span>
                                        )}
                                        {!componente.is_disabled && !componente.is_readonly && (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        )}
                                    </div>
                                </dd>
                            </div>

                            {/* Additional Configuration */}
                            {componente.default_value && (
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Valor por Defecto</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {componente.default_value}
                                    </dd>
                                </div>
                            )}

                            {componente.placeholder && (
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Placeholder</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {componente.placeholder}
                                    </dd>
                                </div>
                            )}

                            {componente.help_text && (
                                <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Texto de Ayuda</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {componente.help_text}
                                    </dd>
                                </div>
                            )}

                            {/* Data Source */}
                            {componente.data_source && componente.data_source.length > 0 && (
                                <div className="bg-gray-50 px-4 py-5 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Fuente de Datos</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div className="max-h-40 overflow-y-auto">
                                            <pre className="text-xs bg-gray-100 p-2 rounded">
                                                {JSON.stringify(componente.data_source, null, 2)}
                                            </pre>
                                        </div>
                                    </dd>
                                </div>
                            )}

                            {/* Event Configuration */}
                            {componente.event_config && Object.keys(componente.event_config).length > 0 && (
                                <div className="bg-white px-4 py-5 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Configuración de Eventos</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div className="max-h-40 overflow-y-auto">
                                            <pre className="text-xs bg-gray-100 p-2 rounded">
                                                {JSON.stringify(componente.event_config, null, 2)}
                                            </pre>
                                        </div>
                                    </dd>
                                </div>
                            )}

                            {/* Form Information */}
                            {componente.formulario && (
                                <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt className="text-sm font-medium text-gray-500">Formulario</dt>
                                    <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <Link
                                            href={`/cajas/formulario-dinamico/${componente.formulario.id}`}
                                            className="text-blue-600 hover:text-blue-800"
                                        >
                                            {componente.formulario.title}
                                        </Link>
                                    </dd>
                                </div>
                            )}

                            {/* Timestamps */}
                            <div className="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">Fechas</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <div>Creado: {formatDate(componente.created_at)}</div>
                                    <div>Actualizado: {formatDate(componente.updated_at)}</div>
                                </dd>
                            </div>

                            {/* ID */}
                            <div className="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt className="text-sm font-medium text-gray-500">ID</dt>
                                <dd className="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {componente.id}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {/* Delete Confirmation Dialog */}
                <Dialog open={confirmOpen} onOpenChange={setConfirmOpen}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Confirmar Eliminación</DialogTitle>
                            <DialogDescription>
                                Esta acción eliminará definitivamente el componente "{componente.label}". No podrás deshacerla.
                            </DialogDescription>
                        </DialogHeader>
                        <DialogFooter>
                            <DialogClose asChild>
                                <button
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                    type="button"
                                    disabled={deleting}
                                >
                                    Cancelar
                                </button>
                            </DialogClose>
                            <button
                                onClick={confirmDelete}
                                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:opacity-50"
                                type="button"
                                disabled={deleting}
                            >
                                {deleting ? 'Eliminando...' : 'Eliminar'}
                            </button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
