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
    const [error, setError] = useState(null)

    // default gallery list
    const [galleryList, setGalleryList] = useState(null)

    // fetch gallery list
    useEffect(() => {
        const fetchGalleryList = async () => {
            // check if user logged in
            try {
                // build request
                const response = await fetch(apiUrl + '/api/gallery/list', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Accept': '*/*'
                    },
                })

                // get response data
                const data = await response.json()

                // check if user token is valid
                if (response.ok && data.status === 'success') {
                    setGalleryList(data.gallery_list)
                    setGalleryImages(data.gallery_list.map((gallery) => ({ gallery, imageUrl: '/default_thumbnail.jpg' })))
                } else {
                    setError('Unable to load galleries (decryption/storage error).')
                    if (DEV_MODE) {
                        console.log('Gallery list fetch error: ' + data.message)
                    }
                    setGalleryList([])
                    setGalleryImages([])
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error to fetch gallery list: ' + error)
                }
                setLoading(false)
                setError('Unable to load galleries (connection/storage error).')
                setGalleryList([])
                setGalleryImages([])
            } finally {
                setLoading(false)
            }
        }
        fetchGalleryList()
    }, [apiUrl, loginToken])

    // fetch gallery thumbnail
    useEffect(() => {
        if (galleryList !== null && galleryList.length > 0) {
            galleryList.forEach(async (gallery) => {
                const imageUrl = await fetchThumbnail(gallery.first_token)
                setGalleryImages((prev) =>
                    prev.map((item) =>
                        item.gallery.name === gallery.name ? { gallery: item.gallery, imageUrl } : item
                    )
                )
            })
        }
    }, [galleryList, loginToken])

    // fetch thumbnail resource
    const fetchThumbnail = async (token) => {
        try {
            const response = await fetch(apiUrl + '/api/thumbnail?token=' + token, {
                method: 'GET',
                credentials: 'include'
            })
    
            // return default thumbnail if status is 500
            if (response.status === 500) {
                return '/default_thumbnail.jpg';
            }
    
            const blob = await response.blob()
            return URL.createObjectURL(blob)
        } catch (error) {
            if (DEV_MODE) {
                console.error('Error to fetch gallery thumbnail: ' + error)
            }
            return '/default_thumbnail.jpg';
        }
    }

    // show loading
    if (loading) {
        return <LoadingComponent/>
    }

    if (error) {
        return <ErrorMessageComponent message={error}/>
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
