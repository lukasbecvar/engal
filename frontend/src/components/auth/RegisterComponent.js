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

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch('http://127.0.0.1:33001/register', {
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

                    console.log('username: ' + username + ', password: ' + password + ', re-password: ' + re_password);
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
