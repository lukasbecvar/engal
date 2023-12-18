import { useEffect, useState } from 'react';

// import engal utils
import { getApiUrl } from '../utils/ApiUtils';
import { appReload } from '../utils/AppUtils';
import { getUserToken, userLogout } from '../utils/AuthUtils';

// import engal components
import UploaderComponent from './UploaderComponent';
import GalleryListComponent from './GalleryListComponent';
import LoadingComponent from './sub-components/LoadingComponent';
import { DEV_MODE } from '../config';

export default function MainComponent() {
    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [upload, setUpload] = useState(false);
    let container = null;

    // get api url
    let api_url = getApiUrl();

    // get user token
    let user_token = getUserToken();

    // user logout
    async function logout() {
        try {
            const formData = new FormData();

            // build request data
            formData.append('token', user_token);

            const response = await fetch(api_url + '/logout', {
                method: 'POST',
                body: formData
            });

            // check error
            if (!response.ok) {
                if (DEV_MODE) {
                    console.error('Error:', response.status);
                }
                return;
            }

            // remove user token form locale storage (logout)
            if (user_token !== null) {
                userLogout();          
            }
        } catch (error) {
            if (DEV_MODE) {
                console.error('Error:', error);
            }
        }
    }

    useEffect(() => {
        // disable loading
        setLoading(false);
    }, []);

    // show upload page
    function showUpload() {
        setUpload(true);
    }

    // disable components for show gallery list
    function showList() {
        appReload();
    }

    // show loading
    if (loading === true) {
        return (<LoadingComponent/>);
    } else {

        // render component to container
        if (upload) {
            container = <UploaderComponent/>;
        } else {
            container = <GalleryListComponent/>;
        }

        return (
            <div className='component'>
                <nav className='navbar navbar-expand-lg navbar-dark bg-dark'>
                    <div className='container-fluid'>
                        <div id='navbarSupportedContent'>
                            <ul className='navbar-nav me-auto'>
                                <li className='nav-item active'>
                                    <button className='nav-link' onClick={(showList)}>List</button>
                                </li>
                            </ul>
                        </div>
                        <div className='d-flex'>
                            <ul className='navbar-nav ms-auto'>
                                <li className='nav-item me-2'>
                                    <button className='nav-link' onClick={showUpload}>Upload</button>
                                </li>
                            </ul>
                            <ul className='navbar-nav ms-auto'>
                                <li className='nav-item'>
                                    <button className='nav-link' onClick={logout}>Logout</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                {container}
            </div>
        );
    }
}
