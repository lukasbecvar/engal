import React, { useState, useEffect } from "react";
import LightGallery from 'lightgallery/react';
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-zoom.css';
import 'lightgallery/css/lg-autoplay.css';
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-fullscreen.css';
import lgZoom from 'lightgallery/plugins/zoom';
import lgAutoplay from 'lightgallery/plugins/autoplay';
import lgFullscreen from 'lightgallery/plugins/fullscreen';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons';
import LoadingComponent from "./sub-component/LoadingComponent";
import NavigationComponent from "./navigation/NavigationComponent";
import BreadcrumbComponent from "./navigation/BreadcrumbComponent";
import ErrorMessageComponent from "./error/ErrorMessageComponent";
import { DEV_MODE, ELEMENTS_PER_PAGE } from "../config";
import { Link } from "react-router-dom";

export default function GalleryBrowserComponent() {
    const apiUrl = localStorage.getItem('api-url');
    const loginToken = localStorage.getItem('login-token');
    const itemsPerPage = ELEMENTS_PER_PAGE;
    const [images, setImages] = useState([]);
    const [error, setError] = useState(null);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            try {
                const galleryName = new URLSearchParams(window.location.search).get('name');
                const response = await fetch(`${apiUrl}/api/gallery/data?gallery_name=${galleryName}`, {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    }
                });
                const data = await response.json();
                if (data.code == 404) {
                    setError(data.message);
                }
                const totalImages = data.gallery_data.length;
                const totalPages = Math.ceil(totalImages / itemsPerPage);
                setTotalPages(totalPages);
                loadImagesForPage(currentPage, data.gallery_data);
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching images: ' + error);
                }
            } finally {
                setTimeout(() => {
                    setLoading(false);
                }, ELEMENTS_PER_PAGE * 150);
            }
        };
        fetchData();
    }, [currentPage]);

    const loadImagesForPage = async (page, data) => {
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = page * itemsPerPage;
        const currentPageData = data.slice(startIndex, endIndex);
        const imagesPromises = currentPageData.map(async (item) => {
            const thumbnailResponse = await fetch(`${apiUrl}/api/thumbnail?token=${item.token}`, {
                headers: {
                    'Authorization': `Bearer ${loginToken}`
                }
            });
            if (!thumbnailResponse.ok) {
                return null;
            }
            const thumbnailBlob = await thumbnailResponse.blob();
            const thumbnailUrl = URL.createObjectURL(thumbnailBlob);
    
            console.log(item.length)

            // If the media is a video, return only the thumbnail
            return { 
                thumbnailUrl, 
                token: item.token,
                name: item.name,
                ownerId: item.ownerId,
                type: item.type,
                length: item.length
            };
        });
        const imagesData = await Promise.all(imagesPromises);
        const validImagesData = imagesData.filter(imageData => imageData !== null);
        setImages(validImagesData);
    };

    const onPageChange = (page) => {
        setCurrentPage(page);
    };

    const onNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };

    const onPrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };

    if (loading) {
        return <LoadingComponent/>;
    }

    if (error) {
        return <ErrorMessageComponent message={error}/>;
    }

    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="browser-component">
    



                <LightGallery licenseKey={'open-source-license'} plugins={[lgZoom, lgFullscreen, lgAutoplay]}>
                    {images.map((mediaData, index) => (
                        mediaData.type.includes('image') ? (
                            <a key={index} href={apiUrl + "/api/media/content?auth_token=" + loginToken +"&media_token=" + mediaData.token} data-lg-type={mediaData.type}>
                                <div className="media-container">
                                    <div className="media-overlay">{mediaData.name}</div>
                                    <img src={mediaData.thumbnailUrl} />
                                </div>
                            </a>
                        ) : null // Add null as an alternative case
                    ))}
                </LightGallery>


                <div className="videos-title"></div>


                {images.map((mediaData, index) => (
                    !mediaData.type.includes('image') ? (
                        <Link key={index} to={"/video?media_token=" + mediaData.token + "&type=" + mediaData.type}>
                            <div className="media-container">
                                <div className="media-overlay">{mediaData.name} ({mediaData.length})</div>
                                <img src={mediaData.thumbnailUrl}></img>
                            </div>
                        </Link>
                    ) : null // Add null as an alternative case
                ))}



                <div className="pagination">
    <button className="arrow-button" onClick={onPrevPage} disabled={currentPage === 1}>
        <FontAwesomeIcon icon={faArrowLeft} />
    </button>
    <div className="show-pages">
        {[...Array(totalPages).keys()].map((page) => (
            ((currentPage === totalPages && page >= Math.max(0, currentPage - 2)) || (page >= Math.max(0, currentPage - 1) && page <= Math.min(totalPages - 1, currentPage + 1))) && (
                <button key={page + 1} onClick={() => onPageChange(page + 1)} className={currentPage === page + 1 ? 'active' : ''}>
                    {page + 1}
                </button>
            )
        ))}
    </div>
    <button className="arrow-button" onClick={onNextPage} disabled={currentPage === totalPages}>
        <FontAwesomeIcon icon={faArrowRight} />
    </button>
</div>



            </div>
        </div>
    );
}
