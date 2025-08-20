import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import App from './app';

// Mount React app
const container = document.getElementById('app');
if (container) {
    const root = createRoot(container);
    root.render(<App />);
} else {
    console.error('Could not find app container element');
}
