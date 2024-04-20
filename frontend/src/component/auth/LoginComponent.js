import React, { useState } from 'react'
import { Link } from 'react-router-dom'

// engal components
import ErrorMessageComponent from '../sub-component/ErrorMessageComponent'

// engal config
import { DEV_MODE } from '../../config'

/**
 * Component Auth/user login
 */
export default function LoginComponent() {
    // get api url form local storage
    let api_url = localStorage.getItem('api-url')
    
    // input states
    const [username, setUsername] = useState('')
    const [password, setPassword] = useState('')

    // status states
    const [error, setError] = useState('')
    const [status, setStatus] = useState(null)

    // login submit
    const handleSubmit = async (e) => {
        e.preventDefault()

        // check if input data is set
        if (username.length < 1 || password.length < 1) {
            setError('Username & password is required inputs')
        } else {
            
            // update process status
            setStatus('processing...')

            try {
                // build login POST request
                const response = await fetch(api_url + '/api/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                })
    
                try {
                    // decode response data
                    const data = await response.json()
            
                    // check if respone is valid
                    if (response.ok) {
                        // set login-token & reinit app
                        localStorage.setItem('login-token', data.token)
                        window.location.href = '/'
                    } else {
                        setError(data.message)
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('ERROR: ' + error)
                    }
                    setError('API connection error')
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.log('ERROR: ' + error)
                }
                setError('API connection error')
            }

            // reset process status
            setStatus(null)
        }
    }

    // handle api error component
    if (error == 'API connection error') {
        return <ErrorMessageComponent message={error}/>
    }

    return (
        <div>
            <h2>Login</h2>
            
            {/* error message box */}
            {error && <p>{error}</p>}
            
            {/* status message box */}
            {status && <p>{status}</p>}

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
    )
}
