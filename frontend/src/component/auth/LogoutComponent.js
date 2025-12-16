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
    const [error, setError] = useState(null)

    useEffect(() => {
        const doLogout = async () => {
            try {
                await fetch(apiUrl + '/api/logout', {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error in logout request: ' + error)
                }
                setError("API connection error")
            } finally {
                localStorage.removeItem('login-token')
                window.location.href = '/'
            }
        }
        doLogout()
    }, [apiUrl])
    
    return (
        <div>
            {error && <ErrorMessageComponent message={error} />}
        </div>
    )
}
