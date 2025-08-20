import React, { useEffect } from 'react';
import { createPortal } from 'react-dom';

const Modal = ({ 
    isOpen, 
    onClose, 
    title, 
    children, 
    size = 'medium',
    closeOnOverlayClick = true,
    showCloseButton = true 
}) => {
    const sizeClasses = {
        small: 'max-w-md',
        medium: 'max-w-lg',
        large: 'max-w-2xl',
        xlarge: 'max-w-4xl'
    };

    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [isOpen]);

    useEffect(() => {
        const handleEscape = (e) => {
            if (e.key === 'Escape' && isOpen) {
                onClose();
            }
        };

        document.addEventListener('keydown', handleEscape);
        return () => document.removeEventListener('keydown', handleEscape);
    }, [isOpen, onClose]);

    if (!isOpen) return null;

    const handleOverlayClick = (e) => {
        if (e.target === e.currentTarget && closeOnOverlayClick) {
            onClose();
        }
    };

    const modalContent = (
        <div className="fixed inset-0 z-50 overflow-y-auto">
            <div 
                className="flex min-h-screen items-center justify-center p-4 text-center sm:p-0"
                onClick={handleOverlayClick}
            >
                <div className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div className={`relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full ${sizeClasses[size]}`}>
                    {/* Header */}
                    {(title || showCloseButton) && (
                        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            {title && (
                                <h3 className="text-lg font-medium text-gray-900">
                                    {title}
                                </h3>
                            )}
                            {showCloseButton && (
                                <button
                                    onClick={onClose}
                                    className="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600"
                                >
                                    <i className="fas fa-times text-xl"></i>
                                </button>
                            )}
                        </div>
                    )}
                    
                    {/* Content */}
                    <div className="px-6 py-4">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );

    return createPortal(modalContent, document.body);
};

const ModalHeader = ({ children, className = '' }) => (
    <div className={`px-6 py-4 border-b border-gray-200 ${className}`}>
        {children}
    </div>
);

const ModalBody = ({ children, className = '' }) => (
    <div className={`px-6 py-4 ${className}`}>
        {children}
    </div>
);

const ModalFooter = ({ children, className = '' }) => (
    <div className={`px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3 ${className}`}>
        {children}
    </div>
);

Modal.Header = ModalHeader;
Modal.Body = ModalBody;
Modal.Footer = ModalFooter;

export default Modal;