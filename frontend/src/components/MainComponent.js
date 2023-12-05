import { useEffect, useState } from 'react'

// import engal utils
import { getApiUrl } from '../utils/ApiUtils'
import { getUserToken, userLogout } from '../utils/AuthUtils'

// import engal components
import LoadingComponent from './sub-components/LoadingComponent'

export default function MainComponent() {
    // state variables for managing component state
    const [loading, setLoading] = useState(true)

    // default username state
    const [username, setUsername] = useState(null)

    // get api url
    let api_url = getApiUrl()

    // get user token
    let user_token = getUserToken()

    async function logout() {
        try {
            const formData = new FormData()

            // build request data
            formData.append('token', user_token)

            const response = await fetch(api_url + '/logout', {
                method: 'POST',
                body: formData
            })

            // check error
            if (!response.ok) {
                console.error('Error:', response.status);
                return;
            }

            // remove user token form locale storage (logout)
            if (user_token != null) {
                userLogout();          
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    useEffect(() => {
        async function fetchData() {
            if (username == null) {
                try {
                    const formData = new FormData();
                    formData.append('token', user_token);
        
                    const response = await fetch(api_url + '/user/status', {
                        method: 'POST',
                        body: formData
                    });
        
                    if (!response.ok) {
                        console.error('Error:', response.status);
                        return;
                    }
                    const data = await response.json();
        
                    if (data.status === 'success') {
                        setUsername(data.username);
                    }
        
                } catch (error) {
                    console.error('Error:', error);
                }
            }
            setLoading(false);
        }
    
        fetchData();
    }, []);

    // show loading
    if (loading == true) {
        return (<LoadingComponent/>);
    } else {
        return (
            <div className='component'>
                <nav className='navbar navbar-expand-lg navbar-dark bg-dark'>
                    <div className='container-fluid'>
                        <button className='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
                            <span className='navbar-toggler-icon'></span>
                        </button>
                        <div className='collapse navbar-collapse' id='navbarSupportedContent'>
                            <ul className='navbar-nav me-auto'>
                            
                                <li className='nav-item active'>
                                    <a className='nav-link' href='#'>List</a>
                                </li>
                            
                                <li className='nav-item'>
                                    <a className='nav-link' href='#'>All</a>
                                </li>
                            
                                <li className='nav-item'>
                                    <a className='nav-link' href='#'>Random</a>
                                </li>
                            
                            </ul>
                        </div>
                        <div className='d-flex'>
                           
                            <ul className='navbar-nav ms-auto'>
                                <li className='nav-item me-2'>
                                    <a className='nav-link' href='#'>Upload</a>
                                </li>
                            </ul>
                            
                            <ul className='navbar-nav ms-auto'>
                                <li className='nav-item'>
                                    <a className='nav-link' href='#' onClick={logout}>Logout</a>
                                </li>
                            </ul>
                        
                        </div>
                    </div>
                </nav>


                <div>
                    <p className='text-light'>logged: {localStorage.getItem('user-token')}, user: {username}</p>
                </div>
            </div>
        );
    }
}
