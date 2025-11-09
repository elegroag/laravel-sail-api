import React, { forwardRef, memo } from 'react';

interface InputFieldProps extends React.InputHTMLAttributes<HTMLInputElement> {
    error?: string;
    helperText?: string;
    inputType?: 'text' | 'email' | 'password' | 'number' | 'date' | 'url';
}

const InputField = memo(forwardRef<HTMLInputElement, InputFieldProps>(
    ({ error, helperText, className = '', ...props }, ref) => {
        const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2';
        const errorClasses = error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
        const finalClassName = `${baseClasses} ${errorClasses} ${className}`.trim();

        return (
            <div className="space-y-1">
                <input
                    ref={ref}
                    className={finalClassName}
                    {...props}
                />
                {error && (
                    <p className="text-sm text-red-600">{error}</p>
                )}
                {helperText && !error && (
                    <p className="text-xs text-gray-500">{helperText}</p>
                )}
            </div>
        );
    }
));

InputField.displayName = 'InputField';

export default InputField;
