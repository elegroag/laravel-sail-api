import { useState } from 'react';

type FetchMethod = 'get' | 'post';

interface UseFetchOptions {
    method?: FetchMethod;
    headers?: Record<string, string>;
}

interface UseFetchReturn<T> {
    data: T | null;
    loading: boolean;
    error: string | null;
    execute: (url: string, body?: Record<string, unknown>) => Promise<void>;
}

export function useFetch<T = unknown>(options?: UseFetchOptions): UseFetchReturn<T> {
    const { method = 'post', headers: customHeaders = {} } = options ?? {};

    const [data, setData] = useState<T | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const buildHeaders = (): Record<string, string> => ({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        ...customHeaders,
    });

    const execute = async (url: string, body?: Record<string, unknown>) => {
        setLoading(true);
        setError(null);

        try {
            const res = await fetch(url, {
                method,
                headers: buildHeaders(),
                credentials: 'same-origin',
                body: method === 'post' && body ? JSON.stringify(body) : undefined,
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.message || `Error ${res.status}`);
            }

            const json = await res.json();
            setData(json);
        } catch (e: unknown) {
            setError(e instanceof Error ? e.message : 'Error desconocido');
            setData(null);
        } finally {
            setLoading(false);
        }
    };

    return { data, loading, error, execute };
}
