import React, { useState, useEffect } from 'react'

// engal components
import ErrorMessageComponent from "../error/ErrorMessageComponent"

// engal utils
import { DEV_MODE } from '../../config'

/**
 * Component Auth/user logout
 */
export default function LogoutComponent() {
    // storage data
    let apiUrl = localStorage.getItem('api-url')
    let loginToken = localStorage.getItem('login-token')

    // status states
    const [error, setError] = useState(null)

    // check if user loggedin
    if (loginToken != null) {
        try {
            // make logout request
            fetch(apiUrl + '/api/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + loginToken
                }
            })
    
            // logout
            localStorage.removeItem('login-token')
            window.location.href = '/'
        } catch (error) {
            if (DEV_MODE) {
                console.error('Error in logout request: ' + error)
            }
            setError("API connection error")
        }
    }
    
    return (
        <div>
            {error && <ErrorMessageComponent message={error} />}
        </div>
    )
}
