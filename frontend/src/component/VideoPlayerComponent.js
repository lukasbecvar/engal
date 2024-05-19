import React, { useEffect, useState } from 'react';
import NavigationComponent from './navigation/NavigationComponent';
import BreadcrumbComponent from './navigation/BreadcrumbComponent';
import ErrorMessageComponent from './error/ErrorMessageComponent';
import { DEV_MODE } from '../config';
import LoadingComponent from './sub-component/LoadingComponent';

export default function VideoPlayerComponent() {
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    const videoToken = new URLSearchParams(window.location.search).get('media_token');

    const [loading, setLoading] = useState(true)
    const [mediaInfo, setMediaInfo] = useState(null)

    // fetch video details
    useEffect(() => {
        const fetchUserData = async () => {
            // check if user loggedin
            if (loginToken != null) {
                try {
                    // build request
                    const response = await fetch(apiUrl + '/api/media/info?media_token=' + videoToken, {
                        method: 'GET',
                        headers: {
                            'Accept': '*/*',
                            'Authorization': 'Bearer ' + loginToken
                        },
                    })

                    // get response data
                    const data = await response.json()
                        
                    // check if user tokne is valid
                    if (data.status == 'success') {
                        setMediaInfo(data.media_info)
                    } else {
                        return <ErrorMessageComponent message={data.message}/>                   
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('Error to fetch user data: ' + error)
                    }
                } finally {
                    setLoading(false)
                }
            }
        }
        fetchUserData()
    }, [apiUrl, loginToken])

    if (loading) {
        return <LoadingComponent/>
    }

    return (
        <div className="video-player-component">
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            
            <div className='video-player'>
                <video controls style={{ width: '100%', height: '100%' }}>
                    <source src={apiUrl + "/api/media/content?auth_token=" + loginToken + "&media_token=" + videoToken} />
                    Your browser does not support the video tag.
                </video>
            </div>
            <div className="video-info-line">
                {mediaInfo.name} <span className="media-length-info">{mediaInfo.length}</span>
            </div>
        </div>
    );
}
 