import React from 'react';

const Alert = ({ 
    type = 'info', 
    title, 
    children, 
    onClose, 
    className = '',
    icon: customIcon 
}) => {
    const types = {
        success: {
            containerClass: 'bg-green-50 border-green-200 text-green-800',
            iconClass: 'text-green-400',
            icon: 'fas fa-check-circle'
        },
        error: {
            containerClass: 'bg-red-50 border-red-200 text-red-800',
            iconClass: 'text-red-400',
            icon: 'fas fa-exclamation-circle'
        },
        warning: {
            containerClass: 'bg-yellow-50 border-yellow-200 text-yellow-800',
            iconClass: 'text-yellow-400',
            icon: 'fas fa-exclamation-triangle'
        },
        info: {
            containerClass: 'bg-blue-50 border-blue-200 text-blue-800',
            iconClass: 'text-blue-400',
            icon: 'fas fa-info-circle'
        }
    };

    const config = types[type];
    const icon = customIcon || config.icon;

    return (
        <div className={`border rounded-md p-4 ${config.containerClass} ${className}`}>
            <div className="flex">
                <div className="flex-shrink-0">
                    <i className={`${icon} ${config.iconClass}`}></i>
                </div>
                <div className="ml-3 flex-1">
                    {title && (
                        <h3 className="text-sm font-medium mb-1">
                            {title}
                        </h3>
                    )}
                    <div className="text-sm">
                        {children}
                    </div>
                </div>
                {onClose && (
                    <div className="ml-auto pl-3">
                        <div className="-mx-1.5 -my-1.5">
                            <button
                                onClick={onClose}
                                className={`inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 ${config.iconClass} hover:bg-opacity-20`}
                            >
                                <i className="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default Alert;