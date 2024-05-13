import { Link } from 'react-router-dom'
import { useEffect, useState } from "react"

// engal components
import LoadingComponent from "../sub-component/LoadingComponent"
import AuthFooterComponent from '../sub-component/AuthFooterComponent'
import ErrorMessageComponent from "../sub-component/error/ErrorMessageComponent"

// engal utils
import { DEV_MODE } from "../../config"

/**
 * Component Auth/user registration
 */
export default function RegisterComponent() {
    // storage data
    let apiUrl = localStorage.getItem('api-url')

    // input states
    const [username, setUsername] = useState('')
    const [password, setPassword] = useState('')
    const [rePassword, setRePassword] = useState('')

    // status states
    const [loading, setLoading] = useState(true)
    const [securityPolicy, setSecurityPolicy] = useState([true])
    const [registerStatus, setRegisterStatus] = useState(false)
    const [apiError, setApiError] = useState(null)
    const [status, setStatus] = useState(null)
    const [error, setError] = useState(null)

    // submitregister 
    const handleSubmit = async (e) => {
        e.preventDefault()
        // reset error
        setError(null)

        // validate username
        if (username.length < 1) {
            setError('username input is empty')
        } else if (username.length < securityPolicy.MIN_USERNAME_LENGTH) {
            setError('username must be at least ' + securityPolicy.MIN_USERNAME_LENGTH +' characters long')
        } else if (username.length > securityPolicy.MAX_USERNAME_LENGTH) {
            setError('username must be maximal ' + securityPolicy.MAX_USERNAME_LENGTH +' characters long')

        // validate password
        } else if (password.length < 1) {
            setError('password input is empty')
        } else if (password.length < securityPolicy.MIN_PASSWORD_LENGTH) {
            setError('password must be at least ' + securityPolicy.MIN_PASSWORD_LENGTH +' characters long')
        } else if (password.length > securityPolicy.MAX_PASSWORD_LENGTH) {
            setError('password must be maximal ' + securityPolicy.MAX_PASSWORD_LENGTH +' characters long')
        } else if (password !== rePassword) {
            setError('passwords not matched')

        // register (if input is valid)
        } else {

            // set process state
            setStatus('processing...')

            try {
                // register POST request data
                const formData = new FormData()

                // build request data
                formData.append('username', username)
                formData.append('password', password)
                formData.append('re-password', rePassword)

                // fetch response
                const response = await fetch(apiUrl + '/api/register', {
                    method: 'POST',
                    body: formData,
                })
          
                // decode response data
                const data = await response.json()

                // check if register is success
                if (data.status == 'success') {
                    // set process state
                    setStatus('processing...')

                    try {
                        // login POST request (auto-login)
                        const response = await fetch(apiUrl + '/api/login', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ username, password })
                        })
                  
                        // check if respone is valid
                        if (response.ok) {
                            const data = await response.json()
                            
                            // set login-token & reinit app
                            localStorage.setItem('login-token', data.token)
                            window.location.href = '/'
                        } else {
                            setError('Invalid credentials.')
                        }
                    } catch (error) {
                        if (DEV_MODE) {
                            console.error('ERROR: ' + error)
                        }
                        setError('API connection error')
                    }
                } else {
                    setError(data.message)
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('ERROR: ' + error)
                }
                setApiError('API connection error')
            }

            // reset process state
            setStatus(null)
        }
    }

    // check register status
    useEffect(() => {
        const fetchData = async () => {
            try {
                // build request
                const response = await fetch(apiUrl, { method: 'GET' })
    
                // get response data
                const data = await response.json()
                                
                // set register status
                setRegisterStatus(data.security_policy.REGISTER_ENABLED)

                // set security policy
                setSecurityPolicy({
                    MIN_USERNAME_LENGTH: data.security_policy.MIN_USERNAME_LENGTH,
                    MAX_USERNAME_LENGTH: data.security_policy.MAX_USERNAME_LENGTH,
                    MIN_PASSWORD_LENGTH: data.security_policy.MIN_PASSWORD_LENGTH,
                    MAX_PASSWORD_LENGTH: data.security_policy.MAX_PASSWORD_LENGTH
                })
            } catch (error) {
                if (DEV_MODE) {
                    console.error('ERROR: ' + error)
                }
                setApiError('Error with API connection')
            } finally {
                setLoading(false)
            }
        }

        fetchData()
    }, [apiUrl, securityPolicy])

    // show loading
    if (loading) {
        return <LoadingComponent/>
    }

    // check api error
    if (apiError != null) {
        return <ErrorMessageComponent message={apiError}/>
    }

    // check if register is enabled
    if (registerStatus == 'false') {
        return <ErrorMessageComponent message="New registrations is currently disabled"/>
    }

    return (
        <div className="auth-container">
            {/* login form */}
            <form onSubmit={handleSubmit} className="auth-form">
                <h2>Register</h2>
                
                {/* error message box */}
                {error && <p className="color-red status-box">{error}</p>}
                
                {/* status message box */}
                {status && <p className="color-blue status-box">{status}</p>}
                <div>
                    <label>Username</label>
                    <input
                        type="text"
                        value={username}
                        placeholder="Username"
                        maxLength={securityPolicy.MAX_USERNAME_LENGTH}
                        onChange={(e) => setUsername(e.target.value)}
                    />
                </div>
                <div>
                    <label>Password</label>
                    <input
                        type="password"
                        value={password}
                        placeholder="Password"
                        maxLength={securityPolicy.MAX_PASSWORD_LENGTH}
                        onChange={(e) => setPassword(e.target.value)}
                    />
                </div>
                <div>
                    <label>Password again</label>
                    <input
                        type="password"
                        value={rePassword}
                        placeholder="Password again"
                        maxLength={securityPolicy.MAX_PASSWORD_LENGTH}
                        onChange={(e) => setRePassword(e.target.value)}
                    />
                </div>
                <button type="submit">Register</button>
                <p className="form-link m-t-5 mb-min-5">You have account? <Link to="/login" className="color-blue">Login here</Link></p>
            </form>
            <AuthFooterComponent/>
        </div>
    )
}
