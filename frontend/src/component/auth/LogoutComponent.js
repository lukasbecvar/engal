import React, { useState, useEffect } from 'react';
import ErrorMessageComponent from "../sub-component/ErrorMessageComponent";

export default function LogoutComponent() {
    const [error, setError] = useState(null);

    async function logout() {
        let api_url = localStorage.getItem('api-url')
        let login_token = localStorage.getItem('login-token');

        try {
            const response = await fetch(api_url + '/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + login_token
                }
            });
                    
            const data = await response.json();
    
            if (data.status === 'success') {
                localStorage.removeItem('login-token');
                window.location.href = '/';
            } else {
                setError(data.message);
            }
    
        } catch (error) {
            console.error('error:' + error);
            setError("API connection error");
        }
    }

    useEffect(() => {
        logout();
    }, []);
    
    return (
        <>
            {error && <ErrorMessageComponent message={error} />}
        </>
    );
}
