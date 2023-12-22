import { useState } from 'react';

// import config values
import { DEV_MODE } from '../../../config';

// import engal utils
import { getApiUrl } from '../../../utils/ApiUtils';
import { appReload } from '../../../utils/AppUtils';
import { getUserToken } from '../../../utils/AuthUtils';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function UsernameChangeComponent(props) 
{
    // state variables for managing component state
    const [error_msg, setError] = useState(null);

    // form data
    const [username, setUsername] = useState(null);
    
    // retrieve API URL from local storage
    let api_url = getApiUrl();

    // get current user token
    let user_token = getUserToken();

    // handle change username input
    function usernameChnageHandle(event) {
        setUsername(event.target.value);
    }
    
    // handle form submit
    function usernameChangeSubmit() {
        
        // check if username is valid 
        if (username === null || username.length < 1) {
            setError('your username input is empty');
        } else if (username.includes(' ')) {
            setError('spaces in username is not allowed');
        } else {
            updateUsername(username)
        }
    }

    // update username
    async function updateUsername(username) {
        try {
            const formData = new FormData();

            // build request data
            formData.append('token', user_token);
            formData.append('new_username', username);

            // make post request
            const response = await fetch(api_url + '/account/settings/username', {
                method: 'POST',
                body: formData
            });

            // get response
            const result = await response.json();

            // check error
            if (!response.ok) {
                if (DEV_MODE) {
                    console.error('error: ', response.status);
                }
                return;
            } else {
                // check if status is success
                if (result.status === 'success') {
                    appReload();
                } else {
                    setError(result.message);
                }
            }
        } catch (error) {
            if (DEV_MODE) {
                console.error('error: ', error);
            }
        }
    }

    return (
        <center>
            <div className="form dark-table bg-dark border">
                <h2 className="form-title">Change username</h2>

                {/* error box alert */}
                {error_msg !== null && (
                    <ErrorBoxComponent error_msg={error_msg}/>
                )}

                {/* new username input */}
                <input type="text" className='text-input' autoComplete='off' onChange={usernameChnageHandle} placeholder='Username'/><br/>

                {/* form submit button */}
                <div className="text-center mb-3">
                    <button className="input-button" type="submit" onClick={usernameChangeSubmit}>Change username</button>
                </div>    
                {props.show_panel_element}
            </div>
        </center> 
    );
}
