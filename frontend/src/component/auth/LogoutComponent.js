import React, { useState, useEffect } from 'react'

// engal components
import ErrorMessageComponent from "../sub-component/ErrorMessageComponent"

// engal utils
import { DEV_MODE } from '../../config'

/**
 * Component Auth/user logout
 */
export default function LogoutComponent() {
    // storage data
    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    // status states
    const [error, setError] = useState(null)

    function logout() {
        // check if user loggedin
        if (login_token != null) {
            try {
                // make logout request
                fetch(api_url + '/api/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + login_token
                    }
                })
    
                // logout
                localStorage.removeItem('login-token')
                window.location.href = '/'
            } catch (error) {
                if (DEV_MODE) {
                    console.error('ERROR: ' + error)
                }
                setError("API connection error")
            }
        }
    }

    // call logout
    useEffect(() => {
        logout()
    }, [api_url, login_token])
    
    return (
        <div>
            {error && <ErrorMessageComponent message={error} />}
        </div>
    )
}
