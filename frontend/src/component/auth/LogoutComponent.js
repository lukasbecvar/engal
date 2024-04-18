import React, { useState, useEffect } from 'react';
import ErrorMessageComponent from "../sub-component/ErrorMessageComponent";

export default function LogoutComponent() {
    const [error, setError] = useState(null);

    function logout() {
        let api_url = localStorage.getItem('api-url')
        let login_token = localStorage.getItem('login-token');

        if (login_token != null) {
            try {
                fetch(api_url + '/api/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + login_token
                    }
                });
    
                localStorage.removeItem('login-token')
                window.location.href = '/'
                
        
            } catch (error) {
                console.error('error:' + error);
                setError("API connection error");
            }
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
