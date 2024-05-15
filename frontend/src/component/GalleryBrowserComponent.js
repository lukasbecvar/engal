import React, { useState, useEffect } from "react";

// engal components
import NavigationComponent from "./navigation/NavigationComponent";
import BreadcrumbComponent from "./navigation/BreadcrumbComponent";

// engal utils
import { DEV_MODE, ELEMENTS_PER_PAGE } from "../config";

export default function GalleryBrowserComponent() {
    // get local storage data
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    // default items limit count
    const itemsPerPage = ELEMENTS_PER_PAGE;

    // main gallery data
    const [galleryData, setGalleryData] = useState([]);
    const [images, setImages] = useState([]);

    // status states
    const [loadingMore, setloadingMoreMore] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);

    // main data fetch
    useEffect(() => {
        const fetchData = async () => {
            // enable loading info
            setloadingMoreMore(true);

            try {
                // get gallery name from query parameter
                const galleryName = new URLSearchParams(window.location.search).get('name');

                // get images data
                const response = await fetch(`${apiUrl}/api/gallery/data?gallery_name=${galleryName}`, {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    }
                });

                // decode gallery data
                const data = await response.json();

                // slice the data to get images for the current page
                const startIndex = 0;
                const endIndex = currentPage * itemsPerPage;
                const currentimages = data.gallery_data.slice(startIndex, endIndex);

                // set gallery data
                setGalleryData(data.gallery_data)

                // get images for current page
                const imagesPromises = currentimages.map(async (item) => {

                    // default content endopint
                    let endpoint = 'content'

                    // check if media type is video (get video thumbnail)
                    if (item.type && !item.type.includes('image')) {
                        endpoint = 'thumbnail';
                    }

                    // get media data
                    const imageResponse = await fetch(`${apiUrl}/api/media/${endpoint}?width=300&height=200&token=${item.token}`, {
                        headers: {
                            'Authorization': `Bearer ${loginToken}`
                        }
                    });
                    const blob = await imageResponse.blob();

                    // build image data array
                    return { 
                        imageUrl: URL.createObjectURL(blob), 
                        name: item.name,
                        type: item.type
                    };
                });

                // set image data to images list
                const imagesData = await Promise.all(imagesPromises);
                setImages(imagesData);
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching images: ' + error);
                }
            } finally {
                setloadingMoreMore(false);
            }
        };

        fetchData();
    }, [currentPage]);

    // scroll end detect (infinite scroll)
    useEffect(() => {
        const handleScroll = () => {
            if (
                window.innerHeight + document.documentElement.scrollTop >= document.documentElement.scrollHeight - 100
                && !loadingMore 
                && galleryData.length !== images.length
            ) {
                nextPage()
            }
        };

        window.addEventListener('scroll', handleScroll);

        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, [loadingMore]);

    // next page click detect
    const nextPage = () => {
        setCurrentPage(prevPage => prevPage + 1);
    };
    
    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="browser-component">
                {images.map((mediaData, index) => (
                    <div key={index} className="media-container">
                        <div className="media-overlay">{mediaData.name}</div>
                        <img src={mediaData.imageUrl} alt={`Media ${index}`}/>
                    </div>
                ))}

                {loadingMore && (
                    <div className="loading-m-component">Loading...</div>
                )}

                {!loadingMore && galleryData.length !== images.length && (
                    <div className="next-load-button">
                        <button onClick={nextPage}>Load next</button>
                    </div>
                )}
            </div>
        </div>
    );
}
