import React from 'react';
import Button from '../atoms/Button';

interface SidePanelProps {
    isOpen: boolean;
    onClose: () => void;
    title: string;
    children: React.ReactNode;
    size?: 'sm' | 'md' | 'lg' | 'xl';
}

const SidePanel: React.FC<SidePanelProps> = ({
    isOpen,
    onClose,
    title,
    children,
    size = 'md'
}) => {
    const sizeClasses = {
        sm: 'max-w-md',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl'
    };

    if (!isOpen) return null;

    return (
        <>
            {/* Overlay */}
            <div
                className="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity z-40"
                onClick={onClose}
            />

            {/* Panel */}
            <div className="fixed inset-y-0 right-0 flex z-50">
                <div className={`flex flex-col bg-white shadow-xl ${sizeClasses[size]} w-full`}>
                    {/* Header */}
                    <div className="flex items-center justify-between px-4 py-6 sm:px-6 border-b border-gray-200">
                        <h2 className="text-lg font-medium text-gray-900">{title}</h2>
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={onClose}
                            className="p-1"
                        >
                            <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </Button>
                    </div>

                    {/* Content */}
                    <div className="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                        {children}
                    </div>
                </div>
            </div>
        </>
    );
};

export default SidePanel;
