export default function SimpleTest() {
    const handleClick = () => {
        alert('React is working with SWC!');
    };

    return (
        <div style={{
            minHeight: '100vh',
            backgroundColor: '#1e40af',
            color: 'white',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            fontFamily: 'Arial, sans-serif'
        }}>
            <div style={{ textAlign: 'center' }}>
                <h1 style={{ 
                    fontSize: '3rem', 
                    marginBottom: '1rem',
                    fontWeight: 'bold'
                }}>
                    ✅ React + SWC Working!
                </h1>
                <p style={{ 
                    fontSize: '1.2rem', 
                    marginBottom: '2rem' 
                }}>
                    Laravel + Vite + React SWC integration successful
                </p>
                <button 
                    onClick={handleClick}
                    style={{
                        backgroundColor: '#3b82f6',
                        color: 'white',
                        padding: '12px 24px',
                        border: 'none',
                        borderRadius: '6px',
                        fontSize: '1rem',
                        cursor: 'pointer',
                        fontWeight: '500'
                    }}
                >
                    Test SWC Button
                </button>
            </div>
        </div>
    );
}