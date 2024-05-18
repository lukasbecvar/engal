import React from 'react';

export default function VideoPlayerComponent() {
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    const videoToken = new URLSearchParams(window.location.search).get('media_token');
    const type = new URLSearchParams(window.location.search).get('type');


    return (
        <div>
                <video controls>
                    <source src={apiUrl + "/api/media/content?auth_token=" + loginToken + "&media_token=" + videoToken} type={type} />
                    Your browser does not support the video tag.
                </video>
        </div>
    );
}
