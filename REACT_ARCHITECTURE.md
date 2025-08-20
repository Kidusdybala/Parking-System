# React SPA Architecture Documentation

## 🏗️ **Complete Architecture Overview**

This document outlines the comprehensive React Single Page Application (SPA) architecture implemented for the MikiPark parking management system.

## 📁 **Folder Structure**

```
resources/js/
├── 📄 App.jsx                    # Main application component with routing
├── 📄 index.jsx                  # Application entry point
├── 📁 pages/                     # Page-level components (route handlers)
│   ├── LoginPage.jsx             # Authentication - Login
│   ├── RegisterPage.jsx          # Authentication - Registration  
│   ├── DashboardPage.jsx         # Main dashboard with stats
│   ├── ParkingPage.jsx           # Parking spot management
│   ├── ReservationsPage.jsx      # Reservation management
│   ├── ProfilePage.jsx           # User profile & settings
│   └── AdminPage.jsx             # Admin dashboard & management
├── 📁 components/                # Reusable UI components
│   ├── ui/                       # Core UI component library
│   │   ├── Button.jsx            # Reusable button component
│   │   ├── Input.jsx             # Form input with validation
│   │   ├── Card.jsx              # Flexible card container
│   │   ├── Modal.jsx             # Portal-based modal
│   │   ├── Alert.jsx             # Notification component
│   │   ├── Badge.jsx             # Status badges
│   │   ├── Table.jsx             # Data table component
│   │   └── index.js              # UI components barrel export
│   ├── Layout/
│   │   └── Layout.jsx            # Main application layout
│   └── Common/
│       └── LoadingSpinner.jsx    # Loading state component
├── 📁 hooks/                     # Custom React hooks
│   ├── useApi.js                 # Generic API request hook
│   ├── useParkingSpots.js        # Parking spots business logic
│   ├── useReservations.js        # Reservations business logic
│   ├── useLocalStorage.js        # Persistent storage hook
│   ├── useForm.js                # Form state management
│   ├── useDebounce.js            # Debounced values
│   └── index.js                  # Hooks barrel export
├── 📁 contexts/                  # React Context providers
│   └── AuthContext.jsx           # Authentication state management
├── 📁 services/                  # API service layer
│   ├── authService.js            # Authentication API calls
│   └── parkingService.js         # Parking-related API calls
└── 📁 utils/                     # Utility functions & constants
    ├── constants.js              # App constants & API endpoints
    ├── helpers.js                # Utility functions
    └── index.js                  # Utils barrel export
```

## 🎯 **Architectural Principles**

### **1. Separation of Concerns**
- **Pages**: Route-level components that compose smaller components
- **Components**: Reusable UI building blocks
- **Hooks**: Business logic and state management
- **Services**: API communication layer
- **Utils**: Pure utility functions

### **2. Component Composition**
- Small, focused components that do one thing well
- Composable UI components with flexible props
- Higher-order components for common patterns

### **3. Custom Hooks Pattern**
- Extract business logic from components
- Reusable stateful logic across components
- Clean separation of concerns

### **4. Service Layer**
- Centralized API communication
- Consistent error handling
- Easy to mock for testing

## 🔧 **Key Features**

### **Custom Hooks**

#### `useApi` - Generic API Hook
```jsx
const { get, post, put, delete: del, loading, error } = useApi();

// Usage
const result = await post('/api/reservations', reservationData);
if (result.success) {
    // Handle success
}
```

#### `useParkingSpots` - Parking Business Logic
```jsx
const { 
    spots, 
    filter, 
    loading, 
    applyFilter, 
    refreshSpots 
} = useParkingSpots();
```

#### `useForm` - Form State Management
```jsx
const {
    values,
    errors,
    handleChange,
    handleSubmit,
    isValid
} = useForm(initialValues, validationRules);
```

### **UI Component Library**

#### Flexible Button Component
```jsx
<Button 
    variant="primary" 
    size="large" 
    loading={isLoading}
    icon="fas fa-save"
    onClick={handleSave}
>
    Save Changes
</Button>
```

#### Form Input with Validation
```jsx
<Input
    label="Email Address"
    name="email"
    type="email"
    value={email}
    onChange={handleChange}
    error={errors.email}
    icon="fas fa-envelope"
    required
/>
```

#### Composable Card Component
```jsx
<Card hover>
    <Card.Header>
        <Card.Title>User Profile</Card.Title>
    </Card.Header>
    <Card.Content>
        {/* Content */}
    </Card.Content>
    <Card.Footer>
        {/* Actions */}
    </Card.Footer>
</Card>
```

### **Service Layer**

#### Authentication Service
```jsx
import authService from '../services/authService';

const result = await authService.login(email, password);
if (result.success) {
    // Handle successful login
}
```

#### Parking Service
```jsx
import parkingService from '../services/parkingService';

const spots = await parkingService.getAvailableSpots();
```

## 🚀 **Usage Examples**

### **Creating a New Page**

1. Create the page component in `pages/`
2. Add route to `App.jsx`
3. Use existing hooks and components

```jsx
// pages/NewPage.jsx
import React from 'react';
import { Card, Button } from '../components/ui';
import { useApi } from '../hooks';

const NewPage = () => {
    const { get, loading } = useApi();
    
    return (
        <div className="space-y-6">
            <Card>
                <Card.Header>
                    <Card.Title>New Feature</Card.Title>
                </Card.Header>
                <Card.Content>
                    {/* Content */}
                </Card.Content>
            </Card>
        </div>
    );
};

export default NewPage;
```

### **Creating a Custom Hook**

```jsx
// hooks/useCustomLogic.js
import { useState, useEffect } from 'react';
import { useApi } from './useApi';

export const useCustomLogic = () => {
    const [data, setData] = useState([]);
    const { get, loading, error } = useApi();
    
    const fetchData = async () => {
        const result = await get('/api/custom-endpoint');
        if (result.success) {
            setData(result.data);
        }
    };
    
    useEffect(() => {
        fetchData();
    }, []);
    
    return {
        data,
        loading,
        error,
        refetch: fetchData
    };
};
```

### **Creating a UI Component**

```jsx
// components/ui/NewComponent.jsx
import React from 'react';

const NewComponent = ({ 
    children, 
    variant = 'default',
    className = '',
    ...props 
}) => {
    const variants = {
        default: 'bg-white',
        primary: 'bg-blue-50'
    };
    
    return (
        <div 
            className={`${variants[variant]} ${className}`}
            {...props}
        >
            {children}
        </div>
    );
};

export default NewComponent;
```

## 📊 **Benefits of This Architecture**

### **✅ Maintainability**
- Clear file organization
- Consistent patterns
- Easy to locate and modify code
- Separation of concerns

### **✅ Reusability**
- Component library grows with the app
- Custom hooks eliminate duplicate logic
- Service layer can be used anywhere
- Utility functions prevent repetition

### **✅ Scalability**
- Easy to add new features
- Modular architecture
- Independent components
- Flexible composition

### **✅ Developer Experience**
- IntelliSense support
- Consistent API patterns
- Easy debugging
- Clear data flow

### **✅ Testing**
- Components can be tested in isolation
- Hooks can be tested independently
- Services can be easily mocked
- Clear boundaries for unit tests

## 🔄 **Data Flow**

```
User Interaction → Page Component → Custom Hook → Service → API
                                      ↓
UI Update ← Component State ← Hook State ← Service Response
```

## 🎨 **Styling Strategy**

- **Tailwind CSS**: Utility-first CSS framework
- **Consistent Design System**: Predefined variants and sizes
- **Responsive Design**: Mobile-first approach
- **Component-level Styling**: Encapsulated styles

## 🧪 **Testing Strategy**

- **Unit Tests**: Individual components and hooks
- **Integration Tests**: Component interactions
- **E2E Tests**: Full user workflows
- **Service Tests**: API layer testing

## 📈 **Performance Optimizations**

- **Code Splitting**: Route-based splitting
- **Lazy Loading**: Dynamic imports
- **Memoization**: React.memo and useMemo
- **Debouncing**: User input optimization

## 🔐 **Security Considerations**

- **JWT Authentication**: Secure token-based auth
- **Protected Routes**: Role-based access control
- **Input Validation**: Client and server-side validation
- **XSS Prevention**: Sanitized inputs

This architecture provides a solid foundation for building scalable, maintainable React applications with clear separation of concerns and reusable components.