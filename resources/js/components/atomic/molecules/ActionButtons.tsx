import React, { memo } from 'react';
import Button from '../atoms/Button';

interface ActionButton {
    label: string;
    onClick: () => void;
    variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'warning';
    size?: 'sm' | 'md' | 'lg';
    loading?: boolean;
    disabled?: boolean;
    icon?: React.ReactNode;
}

interface ActionButtonsProps {
    actions: ActionButton[];
    align?: 'left' | 'center' | 'right';
    spacing?: 'sm' | 'md' | 'lg';
    className?: string;
}

const ActionButtons = memo<ActionButtonsProps>(({
    actions,
    align = 'left',
    spacing = 'md',
    className = ''
}) => {
    const alignClasses = {
        left: 'justify-start',
        center: 'justify-center',
        right: 'justify-end'
    };

    const spacingClasses = {
        sm: 'space-x-2',
        md: 'space-x-3',
        lg: 'space-x-4'
    };

    const finalClassName = `flex ${alignClasses[align]} ${spacingClasses[spacing]} ${className}`.trim();

    return (
        <div className={finalClassName}>
            {actions.map((action, index) => (
                <Button
                    key={index}
                    variant={action.variant || 'secondary'}
                    size={action.size || 'md'}
                    loading={action.loading}
                    disabled={action.disabled}
                    onClick={action.onClick}
                    className={action.icon ? 'flex items-center space-x-2' : ''}
                >
                    {action.icon && <span>{action.icon}</span>}
                    <span>{action.label}</span>
                </Button>
            ))}
        </div>
    );
});

ActionButtons.displayName = 'ActionButtons';

export default ActionButtons;
