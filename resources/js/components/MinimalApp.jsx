function MinimalApp() {
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
                    ✅ React is Working!
                </h1>
                <p>Laravel + React integration successful</p>
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
                    onClick={() => alert('React is working!')}
                >
                    Test Button
                </button>
            </div>
        </div>
    );
}

export default MinimalApp;