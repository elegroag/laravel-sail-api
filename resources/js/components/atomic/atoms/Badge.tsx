import React, { memo } from 'react';

type BadgeVariant = 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'danger';
type BadgeSize = 'sm' | 'md' | 'lg';

interface BadgeProps {
    variant?: BadgeVariant;
    size?: BadgeSize;
    children: React.ReactNode;
    className?: string;
}

const Badge = memo<BadgeProps>(({
    variant = 'default',
    size = 'md',
    children,
    className = ''
}) => {
    const baseClasses = 'inline-flex items-center rounded-full font-medium';

    const variantClasses = {
        default: 'bg-gray-100 text-gray-800',
        primary: 'bg-indigo-100 text-indigo-800',
        secondary: 'bg-gray-100 text-gray-800',
        success: 'bg-green-100 text-green-800',
        warning: 'bg-yellow-100 text-yellow-800',
        danger: 'bg-red-100 text-red-800',
    };

    const sizeClasses = {
        sm: 'px-2 py-0.5 text-xs',
        md: 'px-2.5 py-0.5 text-sm',
        lg: 'px-3 py-1 text-base',
    };

    const finalClassName = `${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${className}`.trim();

    return (
        <span className={finalClassName}>
            {children}
        </span>
    );
});

export default Badge;
