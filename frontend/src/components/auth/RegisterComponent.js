import React, { useState, useEffect } from 'react';

import LoginComponent from "./LoginComponent";
import RegisterDisabledComponent from '../errors/RegisterDisabledComponent';
import LoadingComponent from '../sub-components/LoadingComponent';

const RegisterComponent = () => {
    const [loading, setLoading] = useState(true);
    
    const [status, setStatus] = useState(true);

    const [is_login, setLogin] = useState(false); 

    const [error_msg, setErrorMsg] = useState(null);

    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);
    const [re_password, setRePassword] = useState(null);

    let api_url = localStorage.getItem('api-url');

    useEffect(() => {
        const fetchData = async () => {
            
            try {
                const response = await fetch(api_url + '/register', {
                    method: 'POST',
                });

                const data = await response.json();

                // check if registration enabled
                if (data.message === 'Registration is disabled') {
    
                    console.log(response.status + ', ' + data.message);
                    setStatus(false);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        };

        fetchData();

        setLoading(false);
    }, []);

    function showLogin() {
        setLogin(true);
    }

    function handleUsernameInputChange(event) {
        setUsername(event.target.value);
    }

    function handlePasswordInputChange(event) {
        setPassword(event.target.value);
    }

    function handleRePasswordInputChange(event) {
        setRePassword(event.target.value);
    }

    async function register() {
        // check if username is empty
        if (username == null || username == '') {
            setErrorMsg('username is empty!');
        } else {

            // check if password is empty
            if (password == null || password == '') {
                setErrorMsg('password is empty!');
            } else {

                // check if re_password is empty
                if (re_password == null || re_password == '') {
                    setErrorMsg('password again is empty!');
                } else {

                    // check if password is not matched
                    if (password != re_password) {
                        setErrorMsg('passwords not matched!');
                    } else {

                        if (username.length <= 3) {
                            setErrorMsg('Your username should be at least 4 characters');
                        } else {

                            if (password.length <= 7) {
                                setErrorMsg('Your username should be at least 8 characters');
                            } else {

                                try {
                                    const formData = new FormData();
                                    formData.append('username', username);
                                    formData.append('password', password);
                                    formData.append('re-password', re_password);
            
                                    const response = await fetch(api_url + '/register', {
                                        method: 'POST',
                                        body: formData
                                    });
            
                                    if (!response.ok) {
                                        // Ošetření odmítnuté odpovědi (HTTP status není OK)
                                        console.error('Error:', response.status);
                                        return;
                                    }
            
                                    const data = await response.json();
            
                                    
                                    if (data.message == 'Username is already in use') {
                                        setErrorMsg('username: ' + username + ' is already used!');
                                    } else {
                                        if (data.message == 'User: ' + username + ' registred successfully') {
                                            // tady bude loging
                                            console.log('tady bude login call');
                                        }
                                    }
        
                                } catch (error) {
                                    // Ošetření chyb při samotném HTTP požadavku
                                    console.error('Error:', error);
                                }
                            }


                        }

                    }
                }
            }
        } 
    }


    // show loading
    if (loading == true) {
        return (<LoadingComponent/>);
    } else {

        if (is_login == true) {
            return (<LoginComponent/>)
        } else {
            if (status == true) {
                return (
                    <div>
                        {error_msg !== null && (
                            <div>
                                <p>error: {error_msg}</p>
                            </div>
                        )}
        
                        <div>
                            <p>Register</p>
                            <input type="text" name="username" placeholder="Username" onChange={handleUsernameInputChange}/><br/>
                            <input type="password" name="password" placeholder="Password" onChange={handlePasswordInputChange}/><br/>
                            <input type="password" name="re-password" placeholder="Password again" onChange={handleRePasswordInputChange}/><br/>
                            <button type="button" onClick={register}>Reister</button>
                            <button type="button" onClick={showLogin}>Login</button>
                        </div>
                    </div>
                );
            } else {
                return (<RegisterDisabledComponent/>);
            }
        }

    }
};

export default RegisterComponent;
