import React, { memo } from 'react';

interface ErrorMessageProps {
    message: string;
    title?: string;
    variant?: 'inline' | 'block' | 'toast';
    size?: 'sm' | 'md' | 'lg';
    className?: string;
    onDismiss?: () => void;
}

const ErrorMessage = memo<ErrorMessageProps>(({
    message,
    title = 'Error',
    variant = 'inline',
    size = 'md',
    className = '',
    onDismiss
}) => {
    const baseClasses = 'rounded-md p-4';

    const variantClasses = {
        inline: 'border border-red-200 bg-red-50 text-red-800',
        block: 'border border-red-200 bg-red-50 text-red-800',
        toast: 'border border-red-200 bg-red-50 text-red-800 shadow-lg'
    };

    const sizeClasses = {
        sm: 'text-sm',
        md: 'text-base',
        lg: 'text-lg'
    };

    const iconSizes = {
        sm: 'h-4 w-4',
        md: 'h-5 w-5',
        lg: 'h-6 w-6'
    };

    const finalClassName = `${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${className}`.trim();

    return (
        <div className={finalClassName} role="alert">
            <div className="flex">
                <div className="flex-shrink-0">
                    <svg
                        className={`${iconSizes[size]} text-red-400`}
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clipRule="evenodd"
                        />
                    </svg>
                </div>
                <div className="ml-3 flex-1">
                    {title && variant !== 'inline' && (
                        <h3 className="font-medium text-red-800 mb-1">
                            {title}
                        </h3>
                    )}
                    <p className="text-red-700 whitespace-pre-line">
                        {message}
                    </p>
                </div>
                {onDismiss && (
                    <div className="ml-auto pl-3">
                        <button
                            type="button"
                            className="inline-flex rounded-md p-1.5 text-red-400 hover:text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            onClick={onDismiss}
                        >
                            <span className="sr-only">Cerrar</span>
                            <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fillRule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clipRule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                )}
            </div>
        </div>
    );
});

ErrorMessage.displayName = 'ErrorMessage';

export default ErrorMessage;
