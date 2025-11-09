import React, { forwardRef, useState, memo, useCallback } from 'react';

interface TextAreaFieldProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
    error?: string;
    helperText?: string;
    showCounter?: boolean;
    maxLength?: number;
}

const TextAreaField = memo(forwardRef<HTMLTextAreaElement, TextAreaFieldProps>(
    ({ error, helperText, showCounter, maxLength, className = '', value = '', ...props }, ref) => {
        const [currentLength, setCurrentLength] = useState(String(value).length);

        const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2';
        const errorClasses = error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '';
        const finalClassName = `${baseClasses} ${errorClasses} ${className}`.trim();

        const handleChange = useCallback((e: React.ChangeEvent<HTMLTextAreaElement>) => {
            setCurrentLength(e.target.value.length);
            props.onChange?.(e);
        }, [props]);

        return (
            <div className="space-y-1">
                <textarea
                    ref={ref}
                    className={finalClassName}
                    maxLength={maxLength}
                    {...props}
                    onChange={handleChange}
                />
                <div className="flex justify-between items-center">
                    {error ? (
                        <p className="text-sm text-red-600">{error}</p>
                    ) : helperText ? (
                        <p className="text-xs text-gray-500">{helperText}</p>
                    ) : (
                        <div></div>
                    )}
                    {showCounter && maxLength && (
                        <span className={`text-xs ${currentLength > maxLength * 0.9 ? 'text-red-500' : 'text-gray-500'}`}>
                            {currentLength}/{maxLength}
                        </span>
                    )}
                </div>
            </div>
        );
    }
));

TextAreaField.displayName = 'TextAreaField';

export default TextAreaField;
