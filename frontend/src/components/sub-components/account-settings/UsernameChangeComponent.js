import { useState } from 'react';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function UsernameChangeComponent(props) 
{
    const [username, setUsername] = useState(null);
    
    function usernameChnageHandle(event) {
        setUsername(event.target.value);
    }
    
    function usernameChangeSubmit() {
        console.log(username);
    }

    return (
        <center>
            <div className="form dark-table bg-dark border">
                <h2 className="form-title">Change username</h2>

                <ErrorBoxComponent error_msg="idk"/>

                <input type="text" className='text-input' autoComplete='off' onChange={usernameChnageHandle} placeholder='Username'/><br/>

                <div className="text-center mb-3">
                    <button className="input-button" type="submit" onClick={usernameChangeSubmit}>Change username</button>
                </div>    
                {props.show_panel_element}
            </div>
        </center> 
    );
}
