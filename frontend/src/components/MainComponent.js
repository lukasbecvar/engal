import { useEffect, useState } from 'react';

// import config values
import { DEV_MODE } from '../config';

// import engal utils
import { getApiUrl } from '../utils/ApiUtils';
import { appReload } from '../utils/AppUtils';
import { getUserToken, userLogout } from '../utils/AuthUtils';

// import engal components
import UploaderComponent from './UploaderComponent';
import GalleryListComponent from './GalleryListComponent';
import LoadingComponent from './sub-components/LoadingComponent';
import AccountSettingsComponent from './sub-components/AccountSettingsComponent';

export default function MainComponent() 
{
    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [upload, setUpload] = useState(false);
    const [account_settings, setAccountSettings] = useState(false);

    // user data state
    const [username, setUsername] = useState(null);
    const [role, setRole] = useState(null);
    const [profile_image, setProfileImage] = useState(null);

    let container = null;
    let profile_panel = null;

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
                    console.error('error: ', response.status);
                }
                return;
            }

            // remove user token form locale storage (logout)
            if (user_token !== null) {
                userLogout();          
            }
        } catch (error) {
            if (DEV_MODE) {
                console.error('error: ', error);
            }
        }
    }

    useEffect(() => {
        // get user status
        const fetchUserStatus = async () => {
            try {
                const formData = new FormData();
    
                // set post data
                formData.append('token', user_token);

                // send request
                const response = await fetch(api_url + '/user/status', {
                    method: 'POST',
                    body: formData
                });

                // get response
                const result = await response.json();

                // check response
                if (result.status === 'success') {
                    setUsername(result.username);
                    setRole(result.role);
                    setProfileImage(result.profile_pic);
                } else {
                    if (DEV_MODE) {
                        console.error('error fetching user status: ', result.message);
                    }
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('error fetching user status: ', error);
                }
            } finally {
                setLoading(false);
            }
        };

        fetchUserStatus();
    }, [api_url, user_token]);

    useEffect(() => {
        // disable loading
        setLoading(false);
    }, []);

    // show upload page
    function showUpload() {
        setUpload(true);
        setAccountSettings(false);
    }

    // disable components for show gallery list
    function showList() {
        appReload();
    }

    // show account settings component
    function clickOnProfile() {
        setUpload(false);
        setAccountSettings(true);
    }

    // show loading
    if (loading === true) {
        return (<LoadingComponent/>);
    } else {

        // render component to container
        if (upload) {
            container = <UploaderComponent/>;
        } else if (account_settings) {
            container = <AccountSettingsComponent/>;
        } else {
            container = <GalleryListComponent/>;
        }

        // render user panel component
        if (role === 'Owner' || role === 'Admin') {
            profile_panel = <span className='user-panel red-text'>{username}</span>;
        } else {
            profile_panel = <span className='user-panel green-text'>{username}</span>
        }

        return (
            <div className='component'>
                <nav className='navbar navbar-expand-lg navbar-dark bg-dark'>
                    <div className='container-fluid'>
                        <div id='navbarSupportedContent'>
                            <button type='button' onClick={clickOnProfile}>
                                <img className='profile-image' src={"data:image/jpeg;base64," + profile_image} alt="profile-pic"/>
                            </button>

                            {/* include user panel */}
                            {profile_panel}

                        </div>

                        {/* navigation links */}
                        <div className='nav-space'>
                            <button className='nav-link' onClick={(showList)}>LIST</button>
                            <button className='nav-link' onClick={showUpload}>UPLOAD</button>
                            <button className='nav-link' onClick={logout}>LOGOUT</button>
                        </div>
                    </div>
                </nav>
                <div className='main-component'>{container}</div>
            </div>
        );
    }
}
