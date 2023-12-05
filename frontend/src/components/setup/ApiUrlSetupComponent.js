import React, { useState } from 'react';

// import engal utils
import { checkApiAvailability, setApiLink } from '../../utils/ApiUtils';

export default function ApiUrlSetupComponent() {
    // state variables for managing component state
    const [api_url, setApiUrl] = useState('');
    const [error_msg, setErrorMsg] = useState(null);

    async function set() {
        
        // check if url is not empty
        if (api_url.length !== 0) {
            
            // try to set api url
            try {
                // get api response
                const result = await checkApiAvailability(api_url);
    
                // check if api is reachable
                if (result !== null) {
                   
                    // remove trailing slash from the end of the URL
                    const api_url_to_save = api_url.replace(/\/$/, '');

                    // save api url to local storage
                    setApiLink(api_url_to_save);
                } else {
                    setErrorMsg('this api url is unreachable');
                }
            } catch (error) {
                console.log('Error: ' + error);
                setErrorMsg('this api url is unreachable');
            }
        } else {
            setErrorMsg('URL is empty');
        }
    }

    function handleInputChange(event) {
        setApiUrl(event.target.value);
    }

    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            set();
        }
    }

    return (
        <div className='component'>

            <div className='container mt-5'>
                <div className='w-4/5 m-auto text-center'>
                    <div className='mask d-flex align-items-center h-100 gradient-custom-3'>
                        <div className='container h-100'>
                            <div className='row d-flex justify-content-center align-items-center h-100'>
                                <div className='col-12 col-md-9 col-lg-7 col-xl-6'>
                                    <div className='card bg-dark'>
                                        <div className='card-body p-5 text-light'>
                                            <h2 className='text-uppercase text-center mb-3 text-light'>Set your API URL</h2>

                                            {error_msg !== null && (
                                                <div className='alert alert-warning alert-dismissible fade show' role='alert'>
                                                    <strong>Error</strong> {error_msg}
                                                    <button type='button' className='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                </div>
                                            )}

                                            <div className='set-api-url-form'>
                                                <input type='text' placeholder='url' name='api-url' className='form-control form-control-lg mb-0' onChange={handleInputChange} onKeyDown={handleKeyPress}/>
                                                <div className='m-3 justify-content-center'>
                                                    <button type='button' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={set}>Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}