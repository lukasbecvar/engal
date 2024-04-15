/**
 * Component for setting up the API URL.
 * 
 * This component allows users to set up the API URL by entering the URL.
 * 
 * @returns {JSX.Element} SetupComponent
 */
import React, { useState } from 'react';
import { setApiUrlStorage } from '../util/StorageUtil';
import { isValidUrl } from '../util/UrlUtil';

export default function SetupComponent() {
    const [apiUrl, setApiUrl] = useState('');
    const [error, setError] = useState('');

    /**
     * Handles form submission to validate the URL and save it if valid.
     * 
     * @param {Event} event - The form submission event.
     */
    const handleSubmit = async (event) => {
        event.preventDefault();

        if (!isValidUrl(apiUrl)) {
            setError('Invalid URL');
            return;
        }

        // process request to API
        try {
            const response = await fetch(apiUrl);
            const data = await response.json();

            if (response.ok && data.status === 'success') {
                setApiUrlStorage(apiUrl);
            } else {
                if (data.message != null) {
                    setError(data.message);
                } else {
                    setError('Unknown API error');
                }
            }
        } catch (error) {
            setError('API connection error');
        }
    };

    // render setup form component view
    return (
        <div className="setup-component">
            <h1 className='center-text'>Setup API URL</h1>
            
            {/* error box component */}
            {error && <div className="error-message">{error}</div>}
            
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
