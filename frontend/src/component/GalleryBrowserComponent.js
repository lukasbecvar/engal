import React, { useState, useEffect } from "react";

export default function GalleryBrowserComponent() {
    // get local storage data
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    const [galleryData, setGalleryData] = useState([]);
    const [loading, setLoading] = useState(false); // State for indicating loading

    // main data store states
    const [thumbnails, setThumbnails] = useState([]);
    const [galleryName, setGalleryName] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 10; // Number of items per page

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true); // Set loading to true when fetching data

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

                // set gallery name
                setGalleryName(galleryName);

                setGalleryData(data.gallery_data)

                // slice the data to get images for the current page
                const startIndex = 0; // Always start from the beginning
                const endIndex = currentPage * itemsPerPage;
                const currentThumbnails = data.gallery_data.slice(startIndex, endIndex);

                // get thumbnails for current images
                const thumbnailsPromises = currentThumbnails.map(async (item) => {
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
            } catch (error) {
                console.error('Error fetching thumbnails: ' + error);
            } finally {
                setLoading(false); // Set loading to false when data fetching is done
            }
        };

        fetchData();
    }, [currentPage]); // Re-run effect when currentPage changes

    const nextPage = () => {
        setCurrentPage(prevPage => prevPage + 1);
    };
    
    return (
        <div>
            <div className="app-component">
                <p>gallery: {galleryName}</p>
                <div>
                    {thumbnails.map((thumbnailData, index) => (
                        <div key={index}>
                            <img src={thumbnailData.imageUrl} alt={`Thumbnail ${index}`} />
                            <p>{thumbnailData.name}</p>
                        </div>
                    ))}

                    {loading && <p>Načítání...</p>}

                    {!loading && galleryData.length !== thumbnails.length && (
                        <div>
                            <button onClick={nextPage}>Next</button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
