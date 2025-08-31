import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Task } from '@/types';
import { Link } from '@inertiajs/react';

interface Props {
  task: Task;
}

export default function Show({ task }: Props) {
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Tasks', href: route('tasks.index') },
    { title: task.title, href: route('tasks.show', task.id) },
  ];

  return (
    <AppLayout title="Tarea" breadcrumbs={breadcrumbs}>
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-semibold">{task.title}</h1>
        <div className="flex items-center gap-3">
          <Link href={route('tasks.edit', task.id)} className="text-sm text-primary hover:underline">
            Editar
          </Link>
          <Link href={route('tasks.index')} className="text-sm text-muted-foreground hover:underline">
            Volver
          </Link>
        </div>
      </div>

      {task.description && <p className="mt-4 text-sm text-muted-foreground whitespace-pre-wrap">{task.description}</p>}
      {task.completed && <span className="mt-4 inline-block rounded bg-green-100 px-2 py-0.5 text-xs text-green-700 dark:bg-green-900/30 dark:text-green-300">Completada</span>}
    </AppLayout>
  );
}
