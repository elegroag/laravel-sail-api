import { cajasFormSelectClass } from '@/pages/Cajas/styles/cajas-classes';
import Choices, { type InputChoice, type Options as ChoicesOptions } from 'choices.js';
import { useEffect, useId, useRef } from 'react';
import 'choices.js/public/assets/styles/choices.css';

export type CajasSearchSelectOption = {
    value: string;
    label: string;
};

type Props = {
    id?: string;
    name?: string;
    value?: string;
    options: CajasSearchSelectOption[];
    className?: string;
    disabled?: boolean;
    required?: boolean;
    placeholder?: string;
    searchEnabled?: boolean;
    onChange?: (value: string) => void;
    choicesOptions?: Partial<ChoicesOptions>;
};

const defaultConfig: Partial<ChoicesOptions> = {
    silent: true,
    allowHTML: false,
    shouldSort: false,
    searchEnabled: true,
    searchChoices: true,
    itemSelectText: '',
    searchPlaceholderValue: 'Buscar…',
    noResultsText: 'Sin resultados',
    noChoicesText: 'Sin opciones',
    placeholder: true,
};

export default function CajasSearchSelect({
    id,
    name,
    value = '',
    options,
    className,
    disabled = false,
    required = false,
    placeholder = 'Seleccione',
    searchEnabled = true,
    onChange,
    choicesOptions,
}: Props) {
    const reactId = useId();
    const selectId = id ?? `cajas-search-select-${reactId}`;
    const selectRef = useRef<HTMLSelectElement>(null);
    const choicesRef = useRef<Choices | null>(null);
    const onChangeRef = useRef(onChange);
    const suppressChangeRef = useRef(false);
    const optionsKey = options.map((opt) => `${opt.value}:${opt.label}`).join('|');

    onChangeRef.current = onChange;

    useEffect(() => {
        const el = selectRef.current;
        if (!el) {
            return;
        }

        const instance = new Choices(el, {
            ...defaultConfig,
            ...choicesOptions,
            searchEnabled,
            placeholderValue: placeholder,
        });
        choicesRef.current = instance;

        const handleChange = () => {
            if (suppressChangeRef.current) {
                return;
            }
            onChangeRef.current?.(el.value);
        };

        el.addEventListener('change', handleChange);

        return () => {
            el.removeEventListener('change', handleChange);
            instance.destroy();
            choicesRef.current = null;
        };
        // Init once; options/value synced in dedicated effects.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    useEffect(() => {
        const instance = choicesRef.current;
        if (!instance) {
            return;
        }

        const next: InputChoice[] = [
            { value: '', label: placeholder, placeholder: true, selected: !value },
            ...options.map((opt) => ({
                value: String(opt.value),
                label: opt.label,
                selected: String(opt.value) === String(value),
            })),
        ];

        suppressChangeRef.current = true;
        instance.setChoices(next, 'value', 'label', true);
        if (value) {
            instance.setChoiceByValue(String(value));
        } else {
            instance.setChoiceByValue('');
        }
        suppressChangeRef.current = false;
    }, [optionsKey, options, placeholder, value]);

    useEffect(() => {
        const instance = choicesRef.current;
        if (!instance) {
            return;
        }
        if (disabled) {
            instance.disable();
        } else {
            instance.enable();
        }
    }, [disabled]);

    return (
        <div className={`cajas-choices ${className ?? ''}`.trim()}>
            <select
                ref={selectRef}
                id={selectId}
                name={name}
                className={cajasFormSelectClass}
                disabled={disabled}
                required={required}
                defaultValue={value}
            />
        </div>
    );
}
