import { createElement } from 'react';

function BasicApp() {
    return createElement('div', {
        style: {
            minHeight: '100vh',
            backgroundColor: '#1e3a8a',
            color: 'white',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
        }
    }, 
    createElement('div', {
        style: { textAlign: 'center' }
    },
    createElement('h1', {
        style: { fontSize: '2rem', marginBottom: '1rem' }
    }, '✅ React is Working!'),
    createElement('p', null, 'Laravel + React integration successful'),
    createElement('button', {
        style: {
            backgroundColor: '#3b82f6',
            color: 'white',
            padding: '0.5rem 1rem',
            border: 'none',
            borderRadius: '0.25rem',
            marginTop: '1rem',
            cursor: 'pointer'
        },
        onClick: () => alert('React is working!')
    }, 'Test Button')
    ));
}

export default BasicApp;