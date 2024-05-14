import React, { useState, useEffect } from "react";

// engal components
import BreadcrumbComponent from "./navigation/BreadcrumbComponent";
import NavigationComponent from "./navigation/NavigationComponent";

// engal utils
import { DEV_MODE } from "../config";

export default function GalleryBrowserComponent() {
    // get local storage data
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    // main data store states
    const [thumbnails, setThumbnails] = useState([]);
    const [galleryName, setGalleryName] = useState('');

    useEffect(() => {
        const fetchData = async () => {
            try {
                const galleryName = new URLSearchParams(window.location.search).get('name');

                // get images data
                const response = await fetch(`${apiUrl}/api/gallery/data?gallery_name=${galleryName}`, {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    }
                });

                // decode gallery data
                const data = await response.json();

                // get images thumbnails
                const thumbnailsPromises = data.gallery_data.map(async (item) => {
                    const thumbnailResponse = await fetch(`${apiUrl}/api/media/thumbnail?width=200&height=200&token=${item.token}`, {
                        headers: {
                            'Authorization': `Bearer ${loginToken}`
                        }
                    });
                    const blob = await thumbnailResponse.blob();

                    return { 
                        imageUrl: URL.createObjectURL(blob), 
                        name: item.name
                    };
                });

                const thumbnailsData = await Promise.all(thumbnailsPromises);
                setThumbnails(thumbnailsData);
                setGalleryName(galleryName);
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching thumbnails: ' + error);
                }
            }
        };

        fetchData();
    }, []);

    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="app-component">
                <p>gallery: {galleryName}</p>
                <div>
                    {thumbnails.map((thumbnailData, index) => (
                        <div key={index}>
                            <img src={thumbnailData.imageUrl} alt={`Thumbnail ${index}`} />
                            <p>{thumbnailData.name}</p>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
