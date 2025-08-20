import { STATUS_COLORS, USER_ROLES } from './constants';

/**
 * Format currency amount
 */
export const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount || 0);
};

/**
 * Format date
 */
export const formatDate = (date, options = {}) => {
    const defaultOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    };
    
    return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
};

/**
 * Format time
 */
export const formatTime = (date, options = {}) => {
    const defaultOptions = {
        hour: '2-digit',
        minute: '2-digit',
    };
    
    return new Date(date).toLocaleTimeString('en-US', { ...defaultOptions, ...options });
};

/**
 * Format date and time
 */
export const formatDateTime = (date) => {
    return `${formatDate(date)} at ${formatTime(date)}`;
};

/**
 * Get status color class
 */
export const getStatusColor = (status) => {
    return STATUS_COLORS[status] || 'bg-gray-100 text-gray-800';
};

/**
 * Check if user has role
 */
export const hasRole = (user, role) => {
    if (!user || !user.role) return false;
    return user.role >= role;
};

/**
 * Check if user is admin
 */
export const isAdmin = (user) => {
    return hasRole(user, USER_ROLES.ADMIN);
};

/**
 * Check if user is moderator or admin
 */
export const isModerator = (user) => {
    return hasRole(user, USER_ROLES.MODERATOR);
};

/**
 * Capitalize first letter
 */
export const capitalize = (str) => {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
};

/**
 * Truncate text
 */
export const truncate = (text, length = 100) => {
    if (!text) return '';
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
};

/**
 * Generate random ID
 */
export const generateId = () => {
    return Math.random().toString(36).substr(2, 9);
};

/**
 * Debounce function
 */
export const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Throttle function
 */
export const throttle = (func, limit) => {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

/**
 * Deep clone object
 */
export const deepClone = (obj) => {
    if (obj === null || typeof obj !== 'object') return obj;
    if (obj instanceof Date) return new Date(obj.getTime());
    if (obj instanceof Array) return obj.map(item => deepClone(item));
    if (typeof obj === 'object') {
        const clonedObj = {};
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                clonedObj[key] = deepClone(obj[key]);
            }
        }
        return clonedObj;
    }
};

/**
 * Check if object is empty
 */
export const isEmpty = (obj) => {
    if (obj == null) return true;
    if (Array.isArray(obj) || typeof obj === 'string') return obj.length === 0;
    return Object.keys(obj).length === 0;
};

/**
 * Calculate time difference in human readable format
 */
export const timeAgo = (date) => {
    const now = new Date();
    const diffInSeconds = Math.floor((now - new Date(date)) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [unit, seconds] of Object.entries(intervals)) {
        const interval = Math.floor(diffInSeconds / seconds);
        if (interval >= 1) {
            return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`;
        }
    }
    
    return 'Just now';
};

/**
 * Validate email format
 */
export const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

/**
 * Generate avatar URL or initials
 */
export const getAvatarUrl = (user, size = 40) => {
    if (user?.avatar) {
        return user.avatar;
    }
    
    // Generate initials
    const initials = user?.name
        ?.split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2) || '??';
    
    // Generate a color based on the user's name
    const colors = [
        'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
        'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-gray-500'
    ];
    
    const colorIndex = user?.name?.charCodeAt(0) % colors.length || 0;
    const bgColor = colors[colorIndex];
    
    return {
        initials,
        bgColor,
        size
    };
};

/**
 * Format file size
 */
export const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};