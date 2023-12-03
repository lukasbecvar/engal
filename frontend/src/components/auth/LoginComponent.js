import React, { useState } from 'react';

function LoginComponent() {
    
    const [error_msg, setErrorMsg] = useState(null);

    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);

    async function login() {
        // check if username is empty
        if (username == null || username == '') {
            setErrorMsg('username is empty!');
        } else {

            // check if password is empty
            if (password == null || password == '') {
                setErrorMsg('password is empty!');
            } else {

                console.log('username: ' + username + ', password: ' + password);
            }
        } 
    }

    function handleUsernameInputChange(event) {
        setUsername(event.target.value);
    }

    function handlePasswordInputChange(event) {
        setPassword(event.target.value);
    }

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
            </div>
        </div>
    );

}

export default LoginComponent;
