import React, { useEffect, useState } from 'react';

// import config values
import { DEV_MODE } from '../config';

// import engal utils
import { getApiUrl } from '../utils/ApiUtils';
import { getUserToken } from '../utils/AuthUtils';

// import light gallery
import LightGallery from 'lightgallery/react';
import 'lightgallery/css/lightgallery.css';

// light gallery styles
import 'lightgallery/css/lg-zoom.css';
import 'lightgallery/css/lg-autoplay.css';
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-fullscreen.css';
 
// import light gallery plugins
import lgZoom from 'lightgallery/plugins/zoom';
import lgAutoplay from 'lightgallery/plugins/autoplay';
import lgFullscreen from 'lightgallery/plugins/fullscreen';

// import engal components
import ImageComponent from './sub-components/ImageComponent';
import LoadingComponent from './sub-components/LoadingComponent';
import CustomErrorComponent from './errors/CustomErrorComponent';

export default function GalleryBrowserComponent(props) 
{
    // get gallery name
    let gallery_name = props.gallery_name;

    // update app title
    document.title = 'Engal: gallery ' + gallery_name;

    // state variables for managing component state
    const [error, setError] = useState(null);
    
    // content data
    const [image_list, setImageList] = useState([]);
    const [current_index, setCurrentIndex] = useState(0);
    const [image_content_list, setImageContentList] = useState([]);

    // get user token
    let user_token = getUserToken();

    // get api url
    let api_url = getApiUrl();

    // build image list
    useEffect(function() {
        async function fetchData() { // fetch images list
            try {
                const formData = new FormData();
                
                // set request data
                formData.append('token', user_token);
                formData.append('gallery', gallery_name);

                // fetch response
                const response = await fetch(api_url + '/images', {
                    method: 'POST',
                    body: formData,
                });

                // decode data
                const data = await response.json();

                // check if request is success
                if (data.status === 'success') {
                    setImageList(Object.entries(data.images));
                } else {
                    if (DEV_MODE) {
                        console.error('error fetching image list: ' + data.message);
                    }
                    setError(data.message);
                }
            } catch (error) {
                setError(error);
                if (DEV_MODE) {
                    console.error('error fetching image list: ' + error);
                }
            }     
        };

        fetchData();
    }, [api_url, user_token, gallery_name]);

    // build image content list
    useEffect(function() {
        async function fetchNextImageContent() { // fetch image content
            if (current_index < image_list.length) {
                const [id, name] = image_list[current_index];

                try {
                    const formData = new FormData();
                    
                    // set post data
                    formData.append('token', user_token);
                    formData.append('gallery', gallery_name);
                    formData.append('image', name);

                    // fetch response
                    const response = await fetch(api_url + '/image/content', {
                        method: 'POST',
                        body: formData,
                    });

                    // get response data
                    const data = await response.json();

                    if (data.status === 'success') {
                        setImageContentList((prev_list) => [
                            ...prev_list,
                            {id, name: data.image_name, content: data.content}
                        ]);
                    } else {
                        if (DEV_MODE) {
                            console.error('error fetching image content: ' + data.message);
                        }
                        setError(data.message);
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.error('error fetching image content: ' + error);
                    }
                    setError(error);
                }
                setCurrentIndex((prev_index) => prev_index + 1);
            }
        }

        if (current_index < image_list.length) {
            fetchNextImageContent();
        }
    }, [current_index, image_list, api_url, user_token, gallery_name]);

    // check if image loaded 
    if (image_list.length !== image_content_list.length) {
        return <LoadingComponent/>;
    } else {
        if (error !== null) {
            return <CustomErrorComponent error_message={error}/>;
        } else {
            return (
                <div className="gallery-box">
                    <LightGallery licenseKey={'open-source-license'} plugins={[lgZoom, lgFullscreen, lgAutoplay]}>
                        {image_content_list.map(({ id, name, content }) => (
                            <ImageComponent key={id} name={name} image={content}/>
                        ))}
                    </LightGallery>
                    <br/>
                </div>
            );
        }
    }
}
