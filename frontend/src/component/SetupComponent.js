import React, { useState } from 'react'
import { isValidUrl } from '../util/UrlUtil'
import { getApiStatus, isApiAvailable } from '../util/ApiUtils'

export default function SetupComponent() {
    const [apiUrl, setApiUrl] = useState('')
    const [error, setError] = useState('')

    const handleSubmit = async (event) => {
        event.preventDefault()

        if (!isValidUrl(apiUrl)) {
            setError('Invalid URL')
            return
        }

        if (await isApiAvailable(apiUrl)) {

            let api_response = await getApiStatus(apiUrl)

            if (api_response.status == 'success') {
                localStorage.setItem('api-url', apiUrl)
                window.location.reload()
            } else {
                setError(api_response.message)
            }

        } else {
            setError('API connection error')
        }
    };

    // render setup form component view
    return (
        <div>
            <h1>Setup API URL</h1>
            
            {/* error box component */}
            {error && <div>{error}</div>}
            
            {/* api url set form */}
            <form onSubmit={handleSubmit}>
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
    );
}
