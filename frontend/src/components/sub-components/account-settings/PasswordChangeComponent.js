import { useState } from 'react';

// import config values
import { DEV_MODE } from '../../../config';

// import engal utils
import { appReload } from '../../../utils/AppUtils';
import { getApiUrl } from '../../../utils/ApiUtils';
import { getUserToken } from '../../../utils/AuthUtils';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function PasswordChangeComponent(props) 
{
    // state variables for managing component state
    const [error_msg, setError] = useState(null);

    // form data
    const [password, setPassword] = useState(null);
    const [re_password, setRePassword] = useState(null);
    
    // retrieve API URL from local storage
    let api_url = getApiUrl();

    // get current user token
    let user_token = getUserToken();

    function passwordChangeHandle(event) {
        setPassword(event.target.value);
    }

    function passwordReChangeHandle(event) {
        setRePassword(event.target.value);
    }
    
    function passwordChangeSubmit() {
        // rest error value
        setError(null);

        // check if password is valid 
        if (password === null || password.length < 1) {
            setError('your password input is empty');
        } else if (re_password === null || password.re_password < 1) {
            setError('your re-password input is empty');
        } else if (password.includes(' ')) {
            setError('spaces in password is not allowed');
        } else if (password !== re_password) {
            setError('passwords not matched');
        } else {
            updatePassword(password, re_password);
        }
    }

    // update password
    async function updatePassword(password, re_password) {
        try {
            const formData = new FormData();

            // build request data
            formData.append('token', user_token);
            formData.append('password', password);
            formData.append('re_password', re_password);

            // make post request
            const response = await fetch(api_url + '/account/settings/password', {
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
                <h2 className="form-title">Change password</h2>

                {/* error box alert */}
                {error_msg !== null && (
                    <ErrorBoxComponent error_msg={error_msg}/>
                )}

                {/* passwords inputs */}
                <input type="password" className='text-input' autoComplete='off' onChange={passwordChangeHandle} placeholder='Password'/><br/>
                <input type="password" className='text-input' autoComplete='off' onChange={passwordReChangeHandle} placeholder='Re-password'/>
                
                {/* form submit button */}
                <div className="text-center mb-3">
                    <button className="input-button" type="submit" onClick={passwordChangeSubmit}>Change password</button>
                </div>    
                {props.show_panel_element}
            </div>
        </center> 
    );
}
