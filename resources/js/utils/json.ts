// Utilidades JSON seguras y tipadas

export function parseJsonSafe<T>(input: unknown, fallback: T): T {
  if (input == null) return fallback;
  if (typeof input !== 'string') return (input as T) ?? fallback;
  try {
    const parsed = JSON.parse(input) as T;
    return parsed ?? fallback;
  } catch {
    return fallback;
  }
}
