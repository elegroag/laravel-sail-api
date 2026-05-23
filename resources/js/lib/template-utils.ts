/**
 * Template utilities — helpers del template Vue original
 * Copiados de template-vue/src/lib/utils.ts
 */
import { clsx, type ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatCurrency(value: number, currency = 'COP'): string {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency,
  }).format(value)
}

export function formatDate(date: string | Date, locale = 'es-CO'): string {
  return new Intl.DateTimeFormat(locale, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(new Date(date))
}

export function formatDateTime(date: string | Date, locale = 'es-CO'): string {
  return new Intl.DateTimeFormat(locale, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(date))
}

export function truncate(str: string, length: number): string {
  if (str.length <= length) return str
  return str.slice(0, length) + '...'
}