import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type SharedData, type Task } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';

interface Props {
  tasks: Task[];
}

export default function Index({ tasks }: Props) {
  usePage<SharedData>();
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tasks', href: route('tasks.index') },
  ];

  const handleDelete = (id: number) => {
    if (confirm('¿Eliminar esta tarea?')) {
      router.delete(route('tasks.destroy', id));
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-semibold">Tareas</h1>
        <Link href={route('tasks.create')} prefetch className="inline-flex items-center rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground hover:opacity-90">
          Nueva tarea
        </Link>
      </div>

      <div className="mt-6 divide-y rounded-md border">
        {tasks.length === 0 && (
          <div className="p-4 text-sm text-muted-foreground">No hay tareas. Crea la primera.</div>
        )}
        {tasks.map((task) => (
          <div key={task.id} className="flex items-start justify-between gap-4 p-4">
            <div>
              <div className="font-medium">{task.title}</div>
              {task.description && <div className="text-sm text-muted-foreground">{task.description}</div>}
              {task.completed && <span className="mt-1 inline-block rounded bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/30 dark:text-green-300">Completada</span>}
            </div>
            <div className="flex shrink-0 items-center gap-2">
              <Link href={route('tasks.edit', task.id)} prefetch className="text-sm text-primary hover:underline">
                Editar
              </Link>
              <button onClick={() => handleDelete(task.id)} className="text-sm text-red-600 hover:underline">
                Eliminar
              </button>
            </div>
          </div>
        ))}
      </div>
    </AppLayout>
  );
}
