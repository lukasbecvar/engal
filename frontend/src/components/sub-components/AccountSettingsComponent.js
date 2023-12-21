import React, { useEffect, useState } from 'react';

// import config values
import { DEV_MODE } from '../../config';

// import engal utils
import { getApiUrl } from '../../utils/ApiUtils';
import { getUserToken } from '../../utils/AuthUtils';

// import engal components
import LoadingComponent from "./LoadingComponent";
import PasswordChangeComponent from './account-settings/PasswordChangeComponent';
import UsernameChangeComponent from './account-settings/UsernameChangeComponent';
import ProfilePicChangeComponent from './account-settings/ProfilePicChangeComponent';

export default function AccountSettingsComponent() 
{
    // update app title
    document.title = 'Engal: account settings';

    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [state, setState] = useState('panel');

    // user data state
    const [username, setUsername] = useState(null);
    const [profile_image, setProfileImage] = useState(null);

    // get api url
    let api_url = getApiUrl();

    // get user token
    let user_token = getUserToken();

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

    // handle main panel
    function showPanel() {
        setState('panel');
    }

    // handle profile pic change form
    function changeProfilePic() {
        setState('change-pic');
    }

    // handle username change form
    function changeUsername() {
        setState('change-username');
    }

    // handle password change form
    function changePassword() {
        setState('change-password');
    }

    let show_panel_element = 
        <span className='text-light'>
            back to table:
            <button type='button' className='back-to-table' onClick={showPanel}>here</button>
        </span>;

    // show loading
    if (loading) {
        return <LoadingComponent/>
    } else {

        // change profile-pic
        if (state === 'change-pic') {
            return <ProfilePicChangeComponent show_panel_element={show_panel_element}/>;

        // change username
        } else if (state === 'change-username') {
            return <UsernameChangeComponent show_panel_element={show_panel_element}/>;

        // change password
        } else if (state === 'change-password') {
            return <PasswordChangeComponent show_panel_element={show_panel_element}/>;
            
        } else { 
            return (
                <center>
                    <h2 className="page-title">Account settings</h2>
                    <div className="table-responsive account-settings-table">
                        <table className="table table-dark">
                            <tbody>
                                {/* profile pic change link */}
                                <tr className="line-item">
                                    <th scope='row'>profile-pic: 
                                        <img className="profile-pics-admin-settings" src={'data:image/jpeg;base64,' + profile_image} alt="profile-pic"/>
                                    </th>
                                    <th scope='row'>
                                        <button type='button' className='change-button' onClick={changeProfilePic}>change</button>
                                    </th>
                                </tr>

                                {/* username chnage link */}
                                <tr className="line-item">
                                    <th scope='row'>username: {username}</th>
                                    <th scope='row'>
                                        <button type='button' className='change-button' onClick={changeUsername}>change</button>
                                    </th>
                                </tr>

                                {/* password change link */}
                                <tr className="line-item">
                                    <th scope='row'>password: **********</th>
                                    <th scope='row'>
                                        <button type='button' className='change-button' onClick={changePassword}>change</button>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </center>
            );
        }
    }
}
