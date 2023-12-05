import React, { useEffect, useState } from 'react';

// import engal util
import { userLogin } from '../../utils/AuthUtils';

// import engal components
import RegisterComponent from './RegisterComponent';
import LoadingComponent from '../sub-components/LoadingComponent';
import NavigationComponent from '../sub-components/NavigationComponent';

export default function LoginComponent() {
    // retrieve API URL from local storage
    let api_url = localStorage.getItem('api-url');

    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [is_register, setRegister] = useState(false);
    const [error_msg, setErrorMsg] = useState(null);
    
    // login data state
    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);

    // username input changes handler
    function handleUsernameInputChange(event) {
        setUsername(event.target.value);
    }

    // password input changes handler
    function handlePasswordInputChange(event) {
        setPassword(event.target.value);
    }

    // switch to the registration component
    function showRegister() {
        setRegister(true);
    }

    // handle the login process
    async function login() {
        // validation checks for username and password
        if (username === null || username === '') {
            setErrorMsg('Username is empty!');
        } else if (password === null || password === '') {
            setErrorMsg('Password is empty!');
        } else {
            try {
                const formData = new FormData();
                
                // add data to request from data
                formData.append('username', username);
                formData.append('password', password);

                // perform a POST request to the login API endpoint
                const response = await fetch(api_url + '/login', {
                    method: 'POST',
                    body: formData
                });

                // check error
                if (!response.ok) {
                    console.error('Error:', response.status);
                    return
                }

                // get response data
                const data = await response.json();

                // check the response message
                if (data.message === 'Incorrect username or password') {
                    setErrorMsg('Incorrect username or password');
                } else if (data.message === 'login with username: ' + username + ' successfully') {

                    // set login state
                    userLogin(data.token);
                }
            } catch (error) {
                setErrorMsg('request error, please report this to your administrator');
                console.error('Error:', error);
            }
        }
    }

    // set loading state to false after component mount
    useEffect(function() {
        setLoading(false);
    }, []);

    function handleKeyPress(event) {
        if (event.key === 'Enter') {
            login();
        }
    }

    // conditional rendering based on component state
    if (loading) {
        return <LoadingComponent/>;
    } else {
        if (is_register) {
            return <RegisterComponent/>;
        } else {
            return (
                <div className='component'>
                    <NavigationComponent/>

                    <div className='container mt-5'>
                        <div className='w-4/5 m-auto text-center'>
                            <div className='mask d-flex align-items-center h-100 gradient-custom-3'>
                                <div className='container h-100'>
                                    <div className='row d-flex justify-content-center align-items-center h-100'>
                                        <div className='col-12 col-md-9 col-lg-7 col-xl-6'>
                                            <div className='card bg-dark'>
                                                <div className='card-body p-5 text-light'>
                                                    <h2 className='text-uppercase text-center mb-3 text-light'>Login</h2>

                                                    {error_msg !== null && (
                                                        <div className='alert alert-warning alert-dismissible fade show' role='alert'>
                                                            <strong>Error</strong> {error_msg}
                                                            <button type='button' className='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                                        </div>
                                                    )}

                                                    <div className='login-form'>
                                                        <input type='text' name='username' placeholder='Username' className='form-control form-control-lg mb-0' onChange={handleUsernameInputChange} onKeyDown={handleKeyPress}/><br/>
                                                        <input type='password' name='password' placeholder='Password' className='form-control form-control-lg' onChange={handlePasswordInputChange} onKeyDown={handleKeyPress}/><br/>

                                                        <div className='m-3 justify-content-center'>
                                                            <button type='button' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={login}>Login</button>
                                                        </div>

                                                        <p className='text-center mt-3 mb-0 text-light'>
                                                            You can register here:
                                                            <button className='fw-bold text-light' onClick={showRegister}><span className='ml-3'>register</span></button>
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
        }
    }
}
