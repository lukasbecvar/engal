import React, { useState, useEffect } from "react";

export default function GalleryBrowserComponent() {
    // get local storage data
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');

    // main data store states
    const [thumbnails, setThumbnails] = useState([]);
    const [galleryName, setGalleryName] = useState('');
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [selectedPage, setSelectedPage] = useState(1);
    const itemsPerPage = 50; // Number of items per page

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

                // set gallery name
                setGalleryName(galleryName);

                // calculate total pages
                const totalItems = data.gallery_data.length;
                const totalPages = Math.ceil(totalItems / itemsPerPage);
                setTotalPages(totalPages);

                // slice the data to get images for the current page
                const startIndex = (currentPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
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
            }
        };

        fetchData();
    }, [currentPage]); // Re-run effect when currentPage changes

    useEffect(() => {
        // Scroll to top on currentPage change
        window.scrollTo(0, 0);
    }, [currentPage]);

    const nextPage = () => {
        setCurrentPage(prevPage => prevPage + 1);
    };

    const prevPage = () => {
        setCurrentPage(prevPage => prevPage - 1);
    };

    const goToPage = (pageNumber) => {
        setCurrentPage(pageNumber);
        setSelectedPage(pageNumber);
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
                </div>
                <div>
                    {currentPage > 1 && (
                        <button onClick={prevPage}>Previous</button>
                    )}
                    {thumbnails.length === itemsPerPage && (
                        <button onClick={nextPage}>Next</button>
                    )}
                </div>
                <div>
                    <p>Page {selectedPage} of {totalPages}</p>
                    <select onChange={(e) => goToPage(parseInt(e.target.value))} value={selectedPage}>
                        {[...Array(totalPages)].map((_, index) => (
                            <option key={index + 1} value={index + 1}>{index + 1}</option>
                        ))}
                    </select>
                </div>
            </div>
        </div>
    );
}
