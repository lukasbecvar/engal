import React, { useState, useEffect } from 'react';

// import config values
import { DEV_MODE } from '../../config';

// import engal util
import { getApiUrl } from '../../utils/ApiUtils';
import { userLogin } from '../../utils/AuthUtils';

// import engal components
import LoginComponent from './LoginComponent';
import ErrorBoxComponent from '../sub-components/alerts/ErrorBoxComponent';
import NavigationComponent from '../sub-components/NavigationComponent';
import RegisterDisabledComponent from '../errors/RegisterDisabledComponent';

export default function RegisterComponent() 
{
    // update app title
    document.title = 'Engal: register';

    // state variables for managing component state
    const [status, setStatus] = useState(true);
    const [is_login, setLogin] = useState(false);
    
    // error message for handle errors
    const [error_msg, setErrorMsg] = useState(null);

    // form inputs state
    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);
    const [re_password, setRePassword] = useState(null);

    // retrieve API URL from local storage
    let api_url = getApiUrl()

    // hook to fetch registration status when the component mounts
    useEffect(() => {
        async function checkStatus() {
            try {
                const response = await fetch(api_url + '/register', {
                    method: 'POST',
                });
    
                // get response data
                const data = await response.json();
    
                // check if registration is disabled
                if (data.message === 'registration is disabled') {
                    if (DEV_MODE) {
                        console.log(response.status + ', ' + data.message);
                    }
                    setStatus(false);
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('error: ', error);
                }
                setErrorMsg('request error, please report this to your administrator');
            }
        }
    
        checkStatus();
    }, [api_url]);

    // switch to the login component
    function showLogin() {
        setLogin(true);
    }

    // handle username input change
    function handleUsernameInputChange(event) {
        setUsername(event.target.value);
    }

    // handle pasword input change
    function handlePasswordInputChange(event) {
        setPassword(event.target.value);
    }

    // handle re-password input change
    function handleRePasswordInputChange(event) {
        setRePassword(event.target.value);
    }

    // handle the registration process
    async function register() {

        // null error message
        setErrorMsg(null);

        // validation checks for username, password, and re_password
        if (username == null || username === '') {
            setErrorMsg('username is empty');
        } else if (password == null || password === '') {
            setErrorMsg('password is empty');
        } else if (re_password == null || re_password === '') {
            setErrorMsg('password again is empty');
        } else if (password !== re_password) {
            setErrorMsg('passwords do not match');
        } else if (username.length <= 3) {
            setErrorMsg('your username should be at least 4 characters');
        } else if (username.length >= 31) {
            setErrorMsg('your username is to long (maximal 30 characters)');
        } else if (password.length <= 7) {
            setErrorMsg('your password should be at least 8 characters');
        } else if (password.length >= 51) {
            setErrorMsg('your password is to long (maximal 50 characters)');
        } else if (username.includes(' ') || password.includes(' ') || re_password.includes(' ')) {
            setErrorMsg('spaces in login credentials is not allowed');
        } else {
            try {
                // perform a POST request to the registration API endpoint
                const formData = new FormData();

                // build request data
                formData.append('username', username);
                formData.append('password', password);
                formData.append('re-password', re_password);

                // fetch response
                const response = await fetch(api_url + '/register', {
                    method: 'POST',
                    body: formData,
                });

                // check error
                if (!response.ok) {
                    if (DEV_MODE) {
                        console.error('error: ', response.status);
                    }
                    return;
                }

                // get response data
                const data = await response.json();

                // check the response message
                if (data.message === 'user: ' + username + ' registered successfully') {

                    // set login state
                    userLogin(data.token);
                } else {
                    setErrorMsg(data.message);
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('error: ', error);
                }
                setErrorMsg('request error, please report this to your administrator');
            }
        }
    }

    // handle press enter submit
    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            register();
        }
    }

    // conditional rendering based on component state
    if (is_login) {
        return <LoginComponent/>;
    } else {
        if (status) {
            return (
                <div className='component'>
                    <NavigationComponent/>

                    <div className='container mt-5 mb-5'>
                        <div className='w-4/5 m-auto text-center'>
                            <div className='mask d-flex align-items-center h-100 gradient-custom-3'>
                                <div className='container h-100'>
                                    <div className='row d-flex justify-content-center align-items-center h-100'>
                                        <div className='col-12 col-md-9 col-lg-7 col-xl-6'>
                                            <div className='card bg-dark'>
                                                <div className='card-body p-5 text-light'>
                                                    <h2 className='text-uppercase text-center mb-3 text-light'>Create an account</h2>
                                                
                                                    {error_msg !== null && (
                                                        <ErrorBoxComponent error_msg={error_msg}/>
                                                    )}

                                                    <div className='register-form'>
                                                        <input type='text' name='username' placeholder='Username' className='form-control form-control-lg mb-0' autoComplete='off' onChange={handleUsernameInputChange} onKeyDown={handleKeyPress}/><br/>
                                                        <input type='password' name='password' placeholder='Password' className='form-control form-control-lg mb-0' onChange={handlePasswordInputChange} onKeyDown={handleKeyPress}/><br/>
                                                        <input type='password' name='re-password' placeholder='Password again' className='form-control form-control-lg mb-0' onChange={handleRePasswordInputChange} onKeyDown={handleKeyPress}/><br/>

                                                        <div className='m-3 justify-content-center'>
                                                            <button type='submit' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={register}>Register</button>
                                                        </div>

                                                        <p className='text-center mt-3 mb-0 text-light'>
                                                            Have already an account?
                                                            <button className='fw-bold text-light' onClick={showLogin}><span className='ml-3'>login here</span></button>
                                                        </p>
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
        } else {
            return <RegisterDisabledComponent/>;
        }
    }
}
