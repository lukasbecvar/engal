import React, { useEffect, useState, useRef } from 'react'

// engal components
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './error/ErrorMessageComponent'
import BreadcrumbComponent from './navigation/BreadcrumbComponent'
import NavigationComponent from './navigation/NavigationComponent'

// plyr video player components
import Plyr from 'plyr'
import 'plyr/dist/plyr.css'

// engal utils
import { DEV_MODE } from '../config'

export default function VideoPlayerComponent() {
    // get storage data
    const apiUrl = localStorage.getItem('api-url')
    const loginToken = localStorage.getItem('login-token')

    // get video token
    const videoToken = new URLSearchParams(window.location.search).get('media_token')

    // set states
    const [loading, setLoading] = useState(true)
    const [mediaInfo, setMediaInfo] = useState(null)
    const videoRef = useRef(null)

    // fetch video details
    useEffect(() => {
        const fetchUserData = async () => {
            // check if user logged in
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
                        
                    // check if user token is valid
                    if (data.status === 'success') {
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
    }, [apiUrl, loginToken, videoToken])

    // initialize Plyr
    useEffect(() => {
        if (!loading && videoRef.current) {
            const player = new Plyr(videoRef.current, {
                controls: ['play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen']
                // add any other Plyr options you need
            })
        }
    }, [loading])

    // render loading component
    if (loading) {
        return <LoadingComponent/>
    }

    return (
        <div className="video-player-component">
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            
            {/* render Plyr video player */}
            <div className='video-player'>
                <video ref={videoRef} controls>
                    <source src={apiUrl + "/api/media/content?auth_token=" + loginToken + "&media_token=" + videoToken} type="video/mp4" />
                    Your browser does not support the video tag.
                </video>
            </div>
            <div className="video-info-line">
                {mediaInfo.name} <span className="media-length-info">{mediaInfo.length}</span>
            </div>
        </div>
    )
}
