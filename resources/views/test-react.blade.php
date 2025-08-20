<!DOCTYPE html>
<html>
<head>
    <title>React Test</title>
    <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
</head>
<body>
    <div id="root"></div>
    
    <script type="text/babel">
        function App() {
            return (
                <div style={{
                    minHeight: '100vh',
                    backgroundColor: '#1e3a8a',
                    color: 'white',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center'
                }}>
                    <div style={{ textAlign: 'center' }}>
                        <h1 style={{ fontSize: '2rem', marginBottom: '1rem' }}>
                            ✅ React CDN Test Working!
                        </h1>
                        <p>This proves React can work in your environment</p>
                        <button 
                            style={{
                                backgroundColor: '#3b82f6',
                                color: 'white',
                                padding: '0.5rem 1rem',
                                border: 'none',
                                borderRadius: '0.25rem',
                                marginTop: '1rem',
                                cursor: 'pointer'
                            }}
                            onClick={() => alert('React CDN is working!')}
                        >
                            Test CDN Button
                        </button>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>
</html>