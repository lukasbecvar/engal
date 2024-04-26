import React, { useState } from 'react'

// engal utils
import { isValidUrl } from '../util/UrlUtil'
import { getApiStatus, isApiAvailable } from '../util/ApiUtils'

/**
 * Component API url setup
 */
export default function SetupComponent() {
    // input state
    const [apiUrl, setApiUrl] = useState('')

    // status state
    const [error, setError] = useState('')

    // submit url set
    const handleSubmit = async (event) => {
        event.preventDefault()

        // check if url is valid
        if (!isValidUrl(apiUrl)) {
            setError('Invalid URL')
            return
        }

        // check if API is available
        if (await isApiAvailable(apiUrl)) {
            // get api response
            let api_response = await getApiStatus(apiUrl)

            // check response
            if (api_response.status == 'success') {
                localStorage.setItem('api-url', apiUrl)
                window.location.reload()
            } else {
                setError(api_response.message)
            }
        } else {
            setError('API connection error')
        }
    }

    // render setup form component view
    return (
        <div className="auth-container">
            <form onSubmit={handleSubmit} className="auth-form">
                <h1 className="center-content m-b-3">Setup API URL</h1>
                
                {/* error box component */}
                {error && <p className="color-red status-box">{error}</p>}
                
                {/* api url set form */}
                <label>
                    <input
                        type="text"
                        placeholder="http://localhost:1337"
                        value={apiUrl}
                        onChange={(event) => setApiUrl(event.target.value)}
                    />
                </label>
                <button type="submit">Submit</button>
            </form>
        </div>
    )
}
