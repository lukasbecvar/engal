import React, { useState } from 'react';
import { checkApiAvailability } from '../../utils/apiUtils';

function ApiUrlSetupComponent() {
    const [api_url, setApiUrl] = useState('');
    const [error_msg, setErrorMsg] = useState(null);

    async function set() {
        // check if url is not empty
        if (api_url.length != 0) {
            
            // try to set api url
            try {
                // get api response
                const result = await checkApiAvailability(api_url);
    
                // check if api is reachable
                if (result != null) {
                   
                    // remove trailing slash from the end of the URL
                    const api_url_to_save = api_url.replace(/\/$/, '');

                    // save api url
                    localStorage.setItem('api-url', api_url_to_save);

                    // reload app
                    window.location.reload();
                } else {
                    setErrorMsg('this api url is unreachable')
                }
            } catch (error) {
                setErrorMsg('this api url is unreachable')
            }
        }
    }

    function handleInputChange(event) {
        setApiUrl(event.target.value);
    }

    return (
        <div>
            {error_msg !== null && (
                <div>
                    <p>error: {error_msg}</p>
                </div>
            )}

            <div>
                <p>Set API url</p>
                <input type="text" placeholder="url" name="api-url" value={api_url} onChange={handleInputChange}/>
                <button type="button" onClick={set}>Save</button>
            </div>
        </div>
    );
}

export default ApiUrlSetupComponent;
