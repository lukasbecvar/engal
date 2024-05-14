import { Link } from 'react-router-dom'
import { useEffect, useState } from 'react'

// engal components
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './error/ErrorMessageComponent'

// engal utils
import { DEV_MODE } from '../config'

export default function GalleryListComponent() {
    // get storage data
    let apiUrl = localStorage.getItem('api-url')
    let loginToken = localStorage.getItem('login-token')

    // status state
    const [loading, setLoading] = useState(true)
    const [galleryImages, setGalleryImages] = useState([])
    const [allImagesLoaded, setAllImagesLoaded] = useState(false)

    // default gallery list
    const [galleryList, setGalleryList] = useState(null)

    // fetch gallery list
    useEffect(() => {
        const fetchGalleryList = async () => {
            // check if user logged in
            if (loginToken != null) {
                try {
                    // build request
                    const response = await fetch(apiUrl + '/api/gallery/list', {
                        method: 'GET',
                        headers: {
                            'Accept': '*/*',
                            'Authorization': 'Bearer ' + localStorage.getItem('login-token')
                        },
                    })

                    // get response data
                    const data = await response.json()

                    // check if user token is valid
                    if (data.status === 'success') {
                        setGalleryList(data.gallery_list)
                    } else {
                        return <ErrorMessageComponent message={data.message}/>
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('Error to fetch gallery list: ' + error)
                    }
                } finally {
                    setLoading(false)
                }
            }
        }
        fetchGalleryList()
    }, [apiUrl, loginToken])

    // fetch gallery thumbnail
    useEffect(() => {
        if (galleryList !== null) {
            const fetchGalleryImages = async () => {
                if (loginToken != null) {
                    const images = await Promise.all(
                        // fetch images for all galleries
                        galleryList.map(async (gallery) => {
                            const imageUrl = await fetchThumbnail(gallery.first_token)
                            return { gallery, imageUrl }
                        })
                    )
                    setGalleryImages(images)
                    setAllImagesLoaded(true)
                }
            }
            fetchGalleryImages()
        }
    }, [galleryList, loginToken])

    // fetch thumbnail resource
    const fetchThumbnail = async (token) => {
        // thumbnail resolution
        const width = 500
        const height = 500

        // build api url
        const baseUrl = apiUrl + '/api/media/thumbnail'
        const url = `${baseUrl}?width=${width}&height=${height}&token=${token}`

        // build app header
        const headers = {
            'Authorization': `Bearer ${loginToken}`
        }

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: headers
            })

            const blob = await response.blob()
            return URL.createObjectURL(blob)
        } catch (error) {
            if (DEV_MODE) {
                console.error('Error to fetch gallery thumbnail: ' + error)
            }
            return null
        }
    }

    // show loading
    if (loading || !allImagesLoaded) {
        return <LoadingComponent/>
    }

    return (
        <div className="gallery-container">
            {galleryList !== null && galleryList.length === 0 ? (
                <div>No galleries found.</div>
            ) : (
                galleryImages.map(({ gallery, imageUrl }, index) => (
                    <Link to={"/gallery?name=" + gallery.name} key={gallery.name}>
                        <div key={index} className="gallery-item">
                            <img src={imageUrl} alt={gallery.name}/>
                            <div className="gallery-overlay">
                                <span className="gallery-name">{gallery.name}</span>
                            </div>
                        </div>
                    </Link>
                ))
            )}
        </div>
    )
}
