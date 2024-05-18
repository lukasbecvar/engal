import React from 'react';
import NavigationComponent from './navigation/NavigationComponent';
import BreadcrumbComponent from './navigation/BreadcrumbComponent';

export default function VideoPlayerComponent() {
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    const videoToken = new URLSearchParams(window.location.search).get('media_token');
    const type = new URLSearchParams(window.location.search).get('type');


    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            
            <div className='video-player'>
                <video controls style={{ width: '100%', height: '100%' }}>
                    <source src={apiUrl + "/api/media/content?auth_token=" + loginToken + "&media_token=" + videoToken} type={type} />
                    Your browser does not support the video tag.
                </video>
            </div>
            <div className="video-info-line">
                {videoToken}
            </div>
        </div>
    );
}
