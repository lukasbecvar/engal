import { Link } from "react-router-dom"
import React, { useState, useEffect, useCallback } from "react"

// light gallery styles
// Use bundle css without sourcemap references (avoids lightgallery SCSS warnings)
import 'lightgallery/css/lightgallery-bundle.min.css'

// light gallery components
import LightGallery from 'lightgallery/react'
import lgZoom from 'lightgallery/plugins/zoom'
import lgAutoplay from 'lightgallery/plugins/autoplay'
import lgFullscreen from 'lightgallery/plugins/fullscreen'

// font awesome icons
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons'

// engal components
import LoadingComponent from "./sub-component/LoadingComponent"
import ErrorMessageComponent from "./error/ErrorMessageComponent"
import BreadcrumbComponent from "./navigation/BreadcrumbComponent"
import NavigationComponent from "./navigation/NavigationComponent"

// engal utils
import { DEV_MODE, ELEMENTS_PER_PAGE } from "../config"

export default function GalleryBrowserComponent() {
    // get storage data
    const apiUrl = localStorage.getItem('api-url')
    const loginToken = localStorage.getItem('login-token')
    const [galleryName, setGalleryName] = useState('')

    // set constants
    const itemsPerPage = ELEMENTS_PER_PAGE

    // set data state
    const [images, setImages] = useState([])

    // set states
    const [error, setError] = useState(null)
    const [loading, setLoading] = useState(true)
    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)

    // get gallery data
    useEffect(() => {
        const fetchData = async () => {
            setLoading(true)
            try {
                const currentGalleryName = new URLSearchParams(window.location.search).get('name')
                if (!currentGalleryName) {
                    setError('gallery name is required')
                    return
                }
                setGalleryName(currentGalleryName)

                const response = await fetch(`${apiUrl}/api/gallery/data?gallery_name=${currentGalleryName}`, {
                    credentials: 'include',
                })
                if (!response.ok) {
                    setError('Unable to load gallery (decryption/storage error).')
                    return
                }
                const data = await response.json()
                if (data.status !== 'success' || !Array.isArray(data.gallery_data)) {
                    setError(data.message ?? 'unable to load gallery')
                    return
                }
                const totalImages = data.gallery_data.length
                const totalPages = Math.ceil(totalImages / itemsPerPage)
                setTotalPages(totalPages)
                loadImagesForPage(currentPage, data.gallery_data)
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching images: ' + error)
                }
                setError('unable to load gallery (connection/storage error)')
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [currentPage, apiUrl, loginToken])

    // load thumbnails list
    const loadImagesForPage = async (page, data) => {
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = page * itemsPerPage;
        const currentPageData = data.slice(startIndex, endIndex);
        const imagesPromises = currentPageData.map(async (item) => {
            try {
                const thumbnailResponse = await fetch(`${apiUrl}/api/thumbnail?token=${item.token}`, {
                    credentials: 'include',
                });
    
                if (!thumbnailResponse.ok) {
                    throw new Error('Thumbnail request failed');
                }

                const thumbnailBlob = await thumbnailResponse.blob();
                const thumbnailUrl = URL.createObjectURL(thumbnailBlob);
        
                return { 
                    thumbnailUrl, 
                    token: item.token,
                    name: item.name,
                    ownerId: item.ownerId,
                    type: item.type,
                    length: item.length
                };
            } catch (error) {
                setError((prev) => prev ?? 'Unable to load gallery thumbnails (decryption/storage error).')
                return { 
                    thumbnailUrl: "/default_thumbnail.jpg", // Use default thumbnail URL
                    token: item.token,
                    name: item.name,
                    ownerId: item.ownerId,
                    type: item.type,
                    length: item.length
                };
            }
        });
        
        const imagesData = await Promise.all(imagesPromises);
        const validImagesData = imagesData.filter(imageData => imageData !== null);
        setImages(validImagesData);
    };

    const onPageChange = (page) => {
        setCurrentPage(page)
    }

    const onNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1)
        }
    }

    const onPrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1)
        }
    }

    const interceptDownload = useCallback(() => {
        const handler = async (event) => {
            event.preventDefault()
            event.stopPropagation()

            const href = event.currentTarget.getAttribute('href')
            const filename = event.currentTarget.getAttribute('download') || 'download'

            try {
                const response = await fetch(href, { credentials: 'include' })
                if (!response.ok) {
                    throw new Error('download failed')
                }
                const blob = await response.blob()
                const url = URL.createObjectURL(blob)
                const link = document.createElement('a')
                link.href = url
                link.download = filename
                document.body.appendChild(link)
                link.click()
                link.remove()
                URL.revokeObjectURL(url)
            } catch (err) {
                if (DEV_MODE) {
                    console.error('Download failed: ' + err)
                }
                setError('unable to download file')
            }
        }

        const downloadButtons = document.querySelectorAll('.lg-download')
        downloadButtons.forEach((btn) => {
            btn.removeEventListener('click', handler)
            btn.addEventListener('click', handler)
        })

        return () => {
            downloadButtons.forEach((btn) => btn.removeEventListener('click', handler))
        }
    }, [setError])

    useEffect(() => {
        const cleanup = interceptDownload()
        return () => {
            if (typeof cleanup === 'function') {
                cleanup()
            }
        }
    }, [images, interceptDownload])

    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // show error message
    if (error) {
        return <ErrorMessageComponent message={error}/>
    }

    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="browser-component">
                {/* LightGallery component */}
                <LightGallery
                    licenseKey={'open-source-license'}
                    plugins={[lgZoom, lgFullscreen, lgAutoplay]}
                    download={true}
                    // speed/preload tuned for snappier slide switching
                    speed={90}
                    preload={6}
                    slideEndAnimation={false}
                >
                    {images.map((mediaData, index) => (
                        mediaData.type.includes('image') ? (
                            <a
                                key={index}
                                href={apiUrl + "/api/media/content?media_token=" + mediaData.token}
                                data-lg-type={mediaData.type}
                                data-download-url={apiUrl + "/api/media/content?media_token=" + mediaData.token}
                            >
                                <div className="media-container image-item">
                                    <div className="media-overlay">{mediaData.name}</div>
                                    <img src={mediaData.thumbnailUrl} />
                                </div>
                            </a>
                        ) : null
                    ))}
                </LightGallery>

                {/* video list */}
                <div className="videos-title"></div>
                <div className="video-list">
                    {images.map((mediaData, index) => (
                        !mediaData.type.includes('image') ? (
                            <Link
                                key={index}
                                to={`/video?media_token=${mediaData.token}&gallery_name=${encodeURIComponent(galleryName)}&media_name=${encodeURIComponent(mediaData.name)}`}
                            >
                                <div className="media-container video-item">
                                    <div className="media-overlay">{mediaData.name} ({mediaData.length})</div>
                                    <img src={mediaData.thumbnailUrl}></img>
                                </div>
                            </Link>
                        ) : null
                    ))}
                </div>

                {/* pagination */}
                {totalPages > 1 ? (
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
                ) : (
                    <div className="pagination-spacer" />
                )}
            </div>
        </div>
    )
}
