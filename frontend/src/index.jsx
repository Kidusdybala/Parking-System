import React from 'react';
import { createRoot } from 'react-dom/client';
import './assets/css/main.css';
import App from './app';

// Mount React app
const container = document.getElementById('root');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
} else {
    console.error('Could not find root container element');
}
