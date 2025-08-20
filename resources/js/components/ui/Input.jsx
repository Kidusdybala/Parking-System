import { forwardRef } from 'react';

const Input = forwardRef(({
    label,
    error,
    helperText,
    icon,
    iconPosition = 'left',
    className = '',
    containerClassName = '',
    type = 'text',
    ...props
}, ref) => {
    const baseClasses = 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm';
    const errorClasses = error ? 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500' : '';
    const iconClasses = icon ? (iconPosition === 'left' ? 'pl-10' : 'pr-10') : '';
    
    const inputClasses = `${baseClasses} ${errorClasses} ${iconClasses} ${className}`;

    return (
        <div className={containerClassName}>
            {label && (
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    {label}
                </label>
            )}
            
            <div className="relative">
                {icon && (
                    <div className={`absolute inset-y-0 ${iconPosition === 'left' ? 'left-0 pl-3' : 'right-0 pr-3'} flex items-center pointer-events-none`}>
                        <i className={`${icon} text-gray-400`}></i>
                    </div>
                )}
                
                <input
                    ref={ref}
                    type={type}
                    className={inputClasses}
                    {...props}
                />
            </div>
            
            {error && (
                <p className="mt-1 text-sm text-red-600">{error}</p>
            )}
            
            {helperText && !error && (
                <p className="mt-1 text-sm text-gray-500">{helperText}</p>
            )}
        </div>
    );
});

Input.displayName = 'Input';

export default Input;
