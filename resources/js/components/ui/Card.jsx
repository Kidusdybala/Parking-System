import React from 'react';

const Card = ({ 
    children, 
    className = '', 
    padding = 'medium',
    shadow = 'default',
    hover = false 
}) => {
    const baseClasses = 'bg-white rounded-lg border border-gray-200';
    
    const paddingClasses = {
        none: '',
        small: 'p-4',
        medium: 'p-6',
        large: 'p-8'
    };

    const shadowClasses = {
        none: '',
        small: 'shadow-sm',
        default: 'shadow',
        large: 'shadow-lg'
    };

    const hoverClasses = hover ? 'hover:shadow-lg transition-shadow duration-200' : '';

    const classes = `${baseClasses} ${paddingClasses[padding]} ${shadowClasses[shadow]} ${hoverClasses} ${className}`;

    return (
        <div className={classes}>
            {children}
        </div>
    );
};

const CardHeader = ({ children, className = '' }) => (
    <div className={`border-b border-gray-200 pb-4 mb-4 ${className}`}>
        {children}
    </div>
);

const CardTitle = ({ children, className = '' }) => (
    <h3 className={`text-lg font-medium text-gray-900 ${className}`}>
        {children}
    </h3>
);

const CardContent = ({ children, className = '' }) => (
    <div className={className}>
        {children}
    </div>
);

const CardFooter = ({ children, className = '' }) => (
    <div className={`border-t border-gray-200 pt-4 mt-4 ${className}`}>
        {children}
    </div>
);

Card.Header = CardHeader;
Card.Title = CardTitle;
Card.Content = CardContent;
Card.Footer = CardFooter;

export default Card;