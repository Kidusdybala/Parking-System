// API Endpoints
export const API_ENDPOINTS = {
    AUTH: {
        LOGIN: '/api/auth/login',
        REGISTER: '/api/auth/register',
        LOGOUT: '/api/auth/logout',
        ME: '/api/auth/me',
        REFRESH: '/api/auth/refresh',
        CHANGE_PASSWORD: '/api/auth/change-password'
    },
    PARKING: {
        LIST: '/api/parking-spots',
        SHOW: (id) => `/api/parking-spots/${id}`,
        AVAILABLE: '/api/parking-spots/available/list',
        CREATE: '/api/parking-spots',
        UPDATE: (id) => `/api/parking-spots/${id}`,
        DELETE: (id) => `/api/parking-spots/${id}`,
        RECOMMEND: (userId) => `/api/parking-spots/recommend/${userId}`
    },
    RESERVATIONS: {
        LIST: '/api/reservations',
        ALL: '/api/reservations/all',
        SHOW: (id) => `/api/reservations/${id}`,
        CREATE: '/api/reservations',
        UPDATE: (id) => `/api/reservations/${id}`,
        CANCEL: (id) => `/api/reservations/${id}/cancel`,
        COMPLETE: (id) => `/api/reservations/${id}/complete`,
        STATISTICS: '/api/reservations/statistics'
    },
    USERS: {
        LIST: '/api/users',
        SHOW: (id) => `/api/users/${id}`,
        UPDATE: (id) => `/api/users/${id}`,
        DELETE: (id) => `/api/users/${id}`,
        ADD_BALANCE: (id) => `/api/users/${id}/add-balance`,
        UPDATE_PASSWORD: (id) => `/api/users/${id}/password`,
        STATISTICS: '/api/users/statistics'
    },
    CHAT: {
        SEND_MESSAGE: '/api/chat'
    }
};

// User Roles
export const USER_ROLES = {
    USER: 1,
    MODERATOR: 2,
    ADMIN: 3
};

// Parking Spot Status
export const PARKING_STATUS = {
    AVAILABLE: 'available',
    OCCUPIED: 'occupied',
    RESERVED: 'reserved',
    MAINTENANCE: 'maintenance'
};

// Reservation Status
export const RESERVATION_STATUS = {
    PENDING: 'pending',
    ACTIVE: 'active',
    COMPLETED: 'completed',
    CANCELLED: 'cancelled'
};

// Local Storage Keys
export const STORAGE_KEYS = {
    AUTH_TOKEN: 'auth_token',
    USER_PREFERENCES: 'user_preferences',
    THEME: 'theme'
};

// App Configuration
export const APP_CONFIG = {
    NAME: 'MikiPark',
    VERSION: '1.0.0',
    API_TIMEOUT: 30000,
    TOKEN_REFRESH_THRESHOLD: 5 * 60 * 1000, // 5 minutes
    PAGINATION: {
        DEFAULT_PAGE_SIZE: 10,
        MAX_PAGE_SIZE: 100
    }
};

// Status Colors for UI
export const STATUS_COLORS = {
    [PARKING_STATUS.AVAILABLE]: 'bg-green-100 text-green-800',
    [PARKING_STATUS.OCCUPIED]: 'bg-red-100 text-red-800',
    [PARKING_STATUS.RESERVED]: 'bg-yellow-100 text-yellow-800',
    [PARKING_STATUS.MAINTENANCE]: 'bg-gray-100 text-gray-800',
    
    [RESERVATION_STATUS.PENDING]: 'bg-yellow-100 text-yellow-800',
    [RESERVATION_STATUS.ACTIVE]: 'bg-green-100 text-green-800',
    [RESERVATION_STATUS.COMPLETED]: 'bg-blue-100 text-blue-800',
    [RESERVATION_STATUS.CANCELLED]: 'bg-red-100 text-red-800'
};

// Form Validation Rules
export const VALIDATION_RULES = {
    EMAIL: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    PASSWORD_MIN_LENGTH: 8,
    NAME_MIN_LENGTH: 2,
    NAME_MAX_LENGTH: 255
};