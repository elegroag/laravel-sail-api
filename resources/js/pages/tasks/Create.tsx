import AppLayout from '@/layouts/app-layout';
import TaskForm from '@/components/tasks/task-form';
import { type BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/react';

export default function Create() {
  const { data, setData, post, processing, errors } = useForm({ title: '', description: '', completed: false });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tasks', href: route('tasks.index') },
    { title: 'Crear', href: route('tasks.create') },
  ];

  const submit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    post(route('tasks.store'));
  };

  return (
    <AppLayout title="Crear tarea" breadcrumbs={breadcrumbs}>
      <h1 className="mb-4 text-xl font-semibold">Crear tarea</h1>
      <TaskForm data={data} setData={setData} processing={processing} errors={errors as Record<string, string>} onSubmit={submit} submitLabel="Crear" />
    </AppLayout>
  );
}
