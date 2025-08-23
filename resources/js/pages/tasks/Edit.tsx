import AppLayout from '@/layouts/app-layout';
import TaskForm from '@/components/tasks/task-form';
import { type BreadcrumbItem, type Task } from '@/types';
import { useForm } from '@inertiajs/react';

interface Props {
  task: Task;
}

export default function Edit({ task }: Props) {
  const { data, setData, put, processing, errors } = useForm({
    title: task.title || '',
    description: task.description || '',
    completed: Boolean(task.completed),
  });

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tasks', href: route('tasks.index') },
    { title: 'Editar', href: route('tasks.edit', task.id) },
  ];

  const submit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    put(route('tasks.update', task.id));
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <h1 className="mb-4 text-xl font-semibold">Editar tarea</h1>
      <TaskForm data={data} setData={setData} processing={processing} errors={errors as Record<string, string>} onSubmit={submit} submitLabel="Actualizar" showCompleted />
    </AppLayout>
  );
}
