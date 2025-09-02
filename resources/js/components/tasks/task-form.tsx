import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/input-error';
import { type FormEvent } from 'react';

export type TaskFormData = {
  title: string;
  description?: string;
  completed?: boolean;
};

interface TaskFormProps {
  data: TaskFormData;
  setData: <K extends keyof TaskFormData>(key: K, value: TaskFormData[K]) => void;
  processing?: boolean;
  errors?: Record<string, string>;
  onSubmit: (e: FormEvent<HTMLFormElement>) => void;
  submitLabel: string;
  showCompleted?: boolean;
}

export default function TaskForm({ data, setData, processing = false, errors = {}, onSubmit, submitLabel, showCompleted = false }: TaskFormProps) {
  return (
    <form onSubmit={onSubmit} className="space-y-6">
      <div className="space-y-2">
        <Label htmlFor="title">Título</Label>
        <Input
          id="title"
          name="title"
          value={data.title}
          onChange={(e) => setData('title', e.target.value)}
          placeholder="Ej. Comprar pan"
          required
        />
        <InputError message={errors.title} />
      </div>

      <div className="space-y-2">
        <Label htmlFor="description">Descripción</Label>
        <textarea
          id="description"
          name="description"
          value={data.description || ''}
          onChange={(e) => setData('description', e.target.value)}
          className="w-full min-h-28 rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
          placeholder="Opcional"
        />
        <InputError message={errors.description} />
      </div>

      {showCompleted && (
        <div className="flex items-center gap-2">
          <Checkbox id="completed" checked={!!data.completed} onCheckedChange={(v) => setData('completed', Boolean(v))} />
          <Label htmlFor="completed">Completada</Label>
          <InputError className="ml-2" message={errors.completed} />
        </div>
      )}

      <div className="flex justify-end gap-2">
        <Button type="submit" disabled={processing}>
          {submitLabel}
        </Button>
      </div>
    </form>
  );
}
