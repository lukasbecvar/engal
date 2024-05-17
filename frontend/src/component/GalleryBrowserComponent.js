import React, { useState, useEffect } from "react"

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faArrowLeft, faArrowRight } from '@fortawesome/free-solid-svg-icons'

// engal components
import LoadingComponent from "./sub-component/LoadingComponent"
import NavigationComponent from "./navigation/NavigationComponent"
import BreadcrumbComponent from "./navigation/BreadcrumbComponent"

// engal utils
import { DEV_MODE, ELEMENTS_PER_PAGE } from "../config"

export default function GalleryBrowserComponent() {
    // get local storage data
    const apiUrl = localStorage.getItem('api-url')
    const loginToken = localStorage.getItem('login-token')

    // default items limit count
    const itemsPerPage = ELEMENTS_PER_PAGE

    // main gallery data
    const [images, setImages] = useState([])

    // status states
    const [loading, setLoading] = useState(true)
    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)

    // main data fetch
    useEffect(() => {
        const fetchData = async () => {
            setLoading(true)

            try {
                // get gallery name from query parameter
                const galleryName = new URLSearchParams(window.location.search).get('name')

                // get images data
                const response = await fetch(`${apiUrl}/api/gallery/data?gallery_name=${galleryName}`, {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    }
                })

                // decode gallery data
                const data = await response.json()

                // calculate total pages
                const totalImages = data.gallery_data.length
                const totalPages = Math.ceil(totalImages / itemsPerPage)
                setTotalPages(totalPages)

                // load images for current page
                loadImagesForPage(currentPage, data.gallery_data)
            } catch (error) {
                if (DEV_MODE) {
                    console.error('Error fetching images: ' + error)
                }
            } finally {
                // disable loading (timeout 2s)
                setTimeout(() => {
                    setLoading(false)
                }, 2000);
            }
        }

        fetchData()
    }, [currentPage])

    // load images for specific page
    const loadImagesForPage = async (page, data) => {
        const startIndex = (page - 1) * itemsPerPage
        const endIndex = page * itemsPerPage
        const currentPageData = data.slice(startIndex, endIndex)

        const imagesPromises = currentPageData.map(async (item) => {
            // get media data
            const imageResponse = await fetch(`${apiUrl}/api/thumbnail?token=${item.token}`, {
                headers: {
                    'Authorization': `Bearer ${loginToken}`
                }
            })
            const blob = await imageResponse.blob()

            // build image data array
            return { 
                imageUrl: URL.createObjectURL(blob), 
                name: item.name,
                type: item.type
            }
        })

        // set image data to images list
        const imagesData = await Promise.all(imagesPromises)
        setImages(imagesData)
    }

    // handle page change
    const onPageChange = (page) => {
        setCurrentPage(page)
    }

    // handle next page
    const onNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1)
        }
    }

    // handle previous page
    const onPrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1)
        }
    }

    // show loading
    if (loading) {
        return <LoadingComponent/>
    }

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
    
                <div className="pagination">
                    <button className="arrow-button" onClick={onPrevPage} disabled={currentPage === 1}>
                        <FontAwesomeIcon icon={faArrowLeft} />
                    </button>
                    <div className="show-pages">
                    {[...Array(totalPages).keys()].map((page) => (
                        (page >= currentPage - 1 && page <= currentPage + 1) && (
                            <button key={page+1} onClick={() => onPageChange(page+1)} className={currentPage === page+1 ? 'active' : ''}>
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
    )
}
