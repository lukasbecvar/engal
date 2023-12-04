import React, { useEffect, useState } from 'react';
import RegisterComponent from './RegisterComponent';
import LoadingComponent from '../sub-components/LoadingComponent';

function LoginComponent() {
    
    const [loading, setLoading] = useState(true);

    const [is_register, setRegister] = useState(false); 

    const [error_msg, setErrorMsg] = useState(null);

    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);

    let api_url = localStorage.getItem('api-url');

    function handleUsernameInputChange(event) {
        setUsername(event.target.value);
    }

    function handlePasswordInputChange(event) {
        setPassword(event.target.value);
    }

    function showRegister() {
        setRegister(true);
    }

    async function login() {
        // check if username is empty
        if (username == null || username == '') {
            setErrorMsg('username is empty!');
        } else {

            // check if password is empty
            if (password == null || password == '') {
                setErrorMsg('password is empty!');
            } else {


                try {
                    const formData = new FormData();
                    formData.append('username', username);
                    formData.append('password', password);

                    const response = await fetch(api_url + '/login', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        // Ošetření odmítnuté odpovědi (HTTP status není OK)
                        console.error('Error:', response.status);
                        return;
                    }

                    const data = await response.json();

                    
                    if (data.message == 'Incorrect username or password') {
                        setErrorMsg('Incorrect username or password');
                    } else {
                        if (data.message == 'login with username: ' + username + ' successfully') {
                           
                            localStorage.setItem('user-token', data.token);
                            window.location.reload();
                        }
                    }

                } catch (error) {
                    // Ošetření chyb při samotném HTTP požadavku
                    console.error('Error:', error);
                }

            }
        } 
    }

    useEffect(function() {
        setLoading(false);
    }, []);

    // show loading
    if (loading == true) {
        return (<LoadingComponent/>);
    } else {

        if (is_register == true) {
            return (<RegisterComponent/>);
        } else {
            return (
                <div>
                    {error_msg !== null && (
                        <div>
                            <p>error: {error_msg}</p>
                        </div>
                    )}
        
                    <div>
                        <p>Login</p>
                        <input type="text" name="username" placeholder="Username" onChange={handleUsernameInputChange}/><br/>
                        <input type="text" name="password" placeholder="Password" onChange={handlePasswordInputChange}/><br/>
                        <button type="button" onClick={login}>Login</button>
                        <button type="button" onClick={showRegister}>Register</button>
                    </div>
                </div>
            );
        }
    }
}

export default LoginComponent;
