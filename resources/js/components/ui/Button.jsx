

const Button = ({
    children,
    variant = 'primary',
    size = 'medium',
    disabled = false,
    loading = false,
    icon,
    iconPosition = 'left',
    className = '',
    onClick,
    type = 'button',
    ...props
}) => {
    const baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    const variants = {
        primary: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        secondary: 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500',
        success: 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        warning: 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500',
        outline: 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-blue-500',
        ghost: 'text-gray-700 hover:bg-gray-100 focus:ring-blue-500'
    };

    const sizes = {
        small: 'px-3 py-1.5 text-sm',
        medium: 'px-4 py-2 text-sm',
        large: 'px-6 py-3 text-base'
    };

    const classes = `${baseClasses} ${variants[variant]} ${sizes[size]} ${className}`;

    const renderIcon = (position) => {
        if (!icon || iconPosition !== position) return null;
        
        return (
            <i className={`${icon} ${
                position === 'left' ? 'mr-2' : 'ml-2'
            } ${loading && position === 'left' ? 'hidden' : ''}`}></i>
        );
    };

    const renderLoadingSpinner = () => {
        if (!loading) return null;
        
        return (
            <div className="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
        );
    };

    return (
        <button
            type={type}
            className={classes}
            disabled={disabled || loading}
            onClick={onClick}
            {...props}
        >
            {renderLoadingSpinner()}
            {renderIcon('left')}
            {children}
            {renderIcon('right')}
        </button>
    );
};

export default Button;
