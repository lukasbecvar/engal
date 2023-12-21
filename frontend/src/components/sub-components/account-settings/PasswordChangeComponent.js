import { useState } from 'react';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function PasswordChangeComponent(props) 
{
    const [password, setPassword] = useState(null);
    const [re_password, setRePassword] = useState(null);
    
    function passwordChangeHandle(event) {
        setPassword(event.target.value);
    }

    function passwordReChangeHandle(event) {
        setRePassword(event.target.value);
    }
    
    function passwordChangeSubmit() {
        console.log(password + ", " + re_password);
    }

    return (
        <center>
            <div className="form dark-table bg-dark border">
                <h2 className="form-title">Change password</h2>

                <ErrorBoxComponent error_msg="idk"/>

                <input type="password" className='text-input' autoComplete='off' onChange={passwordChangeHandle} placeholder='Password'/><br/>
                <input type="password" className='text-input' autoComplete='off' onChange={passwordReChangeHandle} placeholder='Re-password'/>
                <div className="text-center mb-3">
                    <button className="input-button" type="submit" onClick={passwordChangeSubmit}>Change password</button>
                </div>    
                {props.show_panel_element}
            </div>
        </center> 
    );
}
