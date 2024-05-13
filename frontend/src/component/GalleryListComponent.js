import { useEffect, useState } from 'react'

// engal components
import ErrorMessageComponent from './sub-component/error/ErrorMessageComponent'

// engal utils
import { DEV_MODE } from '../config'
import LoadingComponent from './sub-component/LoadingComponent'

export default function GalleryListComponent() {
    // get storage data
    let apiUrl = localStorage.getItem('api-url')
    let loginToken = localStorage.getItem('login-token')

    // status state
    const [loading, setLoading] = useState(true)

    // default gallery list
    const [gallery_list, setGalleryList] = useState(null)
    
    useEffect(() => {
        const fetchUserData = async () => {
            // check if user loggedin
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
                        
                    // check if user tokne is valid
                    if (data.status == 'success') {
                        setGalleryList(data.gallery_list)
                    } else {
                        return <ErrorMessageComponent message={data.message}/>                   
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('error: ' + error)
                    }
                } finally {
                    setLoading(false)
                }
            }
        }
        fetchUserData()
    }, [apiUrl, loginToken])

    // show loading
    if (loading) {
        return <LoadingComponent/>
    }

    return (
        <div>
            {gallery_list.map((gallery, index) => (
                <option key={index} value={gallery.name}>{gallery.name} {gallery.first_token}</option>
            ))}
        </div>
    );
}
