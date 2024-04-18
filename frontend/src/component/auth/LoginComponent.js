import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import ErrorMessageComponent from '../sub-component/ErrorMessageComponent';

export default function LoginComponent() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    // get api url form local storage
    let api_url = localStorage.getItem('api-url')

    // login submit
    const handleSubmit = async (e) => {
        e.preventDefault();
        try {
            // build POST request data
            const response = await fetch(api_url + '/api/login_check', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });
      
            // check if respone is valid
            if (response.ok) {
                const data = await response.json();
                
                // set login-token & reinit app
                localStorage.setItem('login-token', data.token);
                window.location.reload(); 
            } else {
                setError('Invalid credentials.');
            }
        } catch (error) {
            console.error('error:' + error);
            setError('API connection error');
        }
    };

    if (error == 'API connection error') {
        return <ErrorMessageComponent message={error}/>
    }

    return (
        <div>
            <h2>Login</h2>
            
            {/* error message box */}
            {error && <p>{error}</p>}
            
            {/* login form */}
            <form onSubmit={handleSubmit}>
                <div>
                    <label>Username:</label>
                    <input
                        type="text"
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                    />
                </div>
                <div>
                    <label>Password:</label>
                    <input
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                    />
                </div>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <Link to="/register">Register here</Link></p>
        </div>
    );
};
