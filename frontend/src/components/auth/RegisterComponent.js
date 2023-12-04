import { useState } from "react";

import LoginComponent from "./LoginComponent";

function RegisterComponent() {
    const [is_login, setLogin] = useState(false); 

    const [error_msg, setErrorMsg] = useState(null);

    const [username, setUsername] = useState(null);
    const [password, setPassword] = useState(null);
    const [re_password, setRePassword] = useState(null);

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

    if (is_login == true) {
        return (<LoginComponent/>)
    } else {
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
    }
}

export default RegisterComponent;
