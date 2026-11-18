import { cajasFormInputClass } from '@/pages/Cajas/styles/cajas-classes';
import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es';
import type { Options } from 'flatpickr/dist/types/options';
import Flatpickr from 'react-flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

type Props = {
    id?: string;
    name?: string;
    value?: string;
    className?: string;
    disabled?: boolean;
    required?: boolean;
    placeholder?: string;
    onChange?: (value: string) => void;
    options?: Options;
};

const baseOptions: Options = {
    dateFormat: 'Y-m-d',
    altInput: true,
    altFormat: 'd/m/Y',
    locale: Spanish,
    allowInput: true,
    disableMobile: true,
};

export default function CajasDateField({
    id,
    name,
    value = '',
    className,
    disabled = false,
    required = false,
    placeholder = 'dd/mm/aaaa',
    onChange,
    options,
}: Props) {
    const inputClass = className ?? cajasFormInputClass;

    return (
        <Flatpickr
            id={id}
            name={name}
            value={value || undefined}
            className={inputClass}
            disabled={disabled}
            required={required}
            placeholder={placeholder}
            options={{
                ...baseOptions,
                ...options,
                altInputClass: [inputClass, options?.altInputClass].filter(Boolean).join(' '),
            }}
            onChange={(_dates, dateStr) => onChange?.(dateStr)}
            onReady={(_dates, _dateStr, instance) => {
                syncAltInputAttrs(instance, { id, required, disabled, placeholder });
            }}
            onValueUpdate={(_dates, _dateStr, instance) => {
                syncAltInputAttrs(instance, { id, required, disabled, placeholder });
            }}
        />
    );
}

function syncAltInputAttrs(
    instance: flatpickr.Instance,
    attrs: { id?: string; required: boolean; disabled: boolean; placeholder: string },
): void {
    const alt = instance.altInput;
    if (!alt) {
        return;
    }

    if (attrs.id) {
        alt.id = attrs.id;
        instance.input.removeAttribute('id');
    }

    alt.required = attrs.required;
    alt.disabled = attrs.disabled;
    alt.placeholder = attrs.placeholder;
}
