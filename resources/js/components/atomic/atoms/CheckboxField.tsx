import React, { forwardRef, memo, useId } from 'react';

interface CheckboxFieldProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'type'> {
    label: string;
    error?: string;
    helperText?: string;
    indeterminate?: boolean;
    labelPosition?: 'left' | 'right';
}

const CheckboxField = memo(forwardRef<HTMLInputElement, CheckboxFieldProps>(
    ({
        label,
        error,
        helperText,
        indeterminate = false,
        labelPosition = 'right',
        className = '',
        id,
        ...props
    }, ref) => {
        const generatedId = useId();
        const checkboxId = id || generatedId;

        const baseClasses = 'h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-colors duration-200';

        const errorClasses = error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';

        const finalClassName = `${baseClasses} ${errorClasses} ${className}`.trim();

        return (
            <div className="space-y-1">
                <div className={`flex items-center ${labelPosition === 'left' ? 'flex-row-reverse' : ''}`}>
                    <input
                        ref={(el) => {
                            if (el) {
                                el.indeterminate = indeterminate;
                            }
                            if (typeof ref === 'function') {
                                ref(el);
                            } else if (ref) {
                                ref.current = el;
                            }
                        }}
                        id={checkboxId}
                        type="checkbox"
                        className={finalClassName}
                        {...props}
                    />
                    <label
                        htmlFor={checkboxId}
                        className={`ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none ${
                            labelPosition === 'left' ? 'mr-2 ml-0' : ''
                        }`}
                    >
                        {label}
                        {props.required && <span className="text-red-500 ml-1">*</span>}
                    </label>
                </div>

                {error && (
                    <p className="text-sm text-red-600 ml-6">{error}</p>
                )}

                {helperText && !error && (
                    <p className="text-xs text-gray-500 ml-6">{helperText}</p>
                )}
            </div>
        );
    }
));

CheckboxField.displayName = 'CheckboxField';

export default CheckboxField;
