import { useEffect, useState } from "react";

// import engal utils
import { getApiUrl } from "../utils/ApiUtils";
import { getUserToken } from "../utils/AuthUtils";

// import engal components
import CustomErrorComponent from "./errors/CustomErrorComponent";
import LoadingComponent from "./sub-components/LoadingComponent";
import GalleryComponent from "./sub-components/GalleryComponent";
import GalleryBrowserComponent from "./GalleryBrowserComponent";
import { DEV_MODE } from "../config";

export default function GalleryListComponent() {
    // retrieve API URL from local storage
    let api_url = getApiUrl();

    // get current user token
    let user_token = getUserToken();

    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [current_gallery, setGallery] = useState(null);

    // gallery list array
    const [gallery_options, setGalleryOptions] = useState([]);

    // set gallery browser
    function showGallery(gallery_name) {
        setGallery(gallery_name);
    }

    useEffect(() => {
        // get gallery list from gallery name selection
        const fetchGalleryList = async () => {
            try {
                const formData = new FormData();
    
                // set post data
                formData.append('token', user_token);

                // send request
                const response = await fetch(api_url + '/gallery/list', {
                    method: 'POST',
                    body: formData
                });

                // get response
                const result = await response.json();

                // check response
                if (result.status === 'success') {
                    const galleryListWithThumbnails = result.gallery_list.map((gallery) => {
                        return {name: gallery.name, count: gallery.images_count, thumbnail: gallery.thumbnail};
                    });
                    setGalleryOptions([...galleryListWithThumbnails]);
                } else {
                    if (DEV_MODE) {
                        console.error('Error fetching gallery list: ', result.message);
                    }
                    setError('error fetching gallery list');
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching gallery list: ', error);
                }
                setError('error fetching gallery list');
            } finally {
                setLoading(false);
            }
        };

        fetchGalleryList();
    }, [api_url, user_token]);

    // show loading
    if (loading === true) {
        return (<LoadingComponent/>);
    } else {
        if (current_gallery !== null) {
            return <GalleryBrowserComponent gallery_name={current_gallery}/>
        } else {
            // check if found error
            if (error !== null) {
                return <CustomErrorComponent error_message={error}/>
            } else {
                // check if gallery list is empty
                if (gallery_options.length === 0) {
                    return (
                        <center className="container mt-5">
                            <h5 className="text-light">
                                Your gallery list is empty
                            </h5>
                        </center>
                    );
                } else {
                    return (
                        <center className='container mt-2'>
                            {gallery_options.map((gallery, index) => (
                                <span key={index} onClick={() => showGallery(gallery.name)}>
                                    <GalleryComponent key={index} name={gallery.name + ' [' + gallery.count + ']'} thumbnail={gallery.thumbnail}/>
                                </span>
                            ))}
                            <br/>
                        </center>
                    );
                }
            }
        }
    }
}
