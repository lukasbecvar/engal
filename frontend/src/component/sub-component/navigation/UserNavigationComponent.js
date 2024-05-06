import { useEffect, useState } from 'react'
import { Link, useLocation } from 'react-router-dom'

// engal components
import LoadingComponent from '../LoadingComponent'
import ErrorMessageComponent from '../error/ErrorMessageComponent'

/**
 * User panel navigation
 */
export default function UserNavigationComponent() {
    // storage data
    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    const location = useLocation();

    // status state
    const [user_data, setUserData] = useState([])
    const [loading, setLoading] = useState(true)
    
    // fetch dashboard/user data
    useEffect(() => {
        const fetchUserData = async () => {
            // check if user loggedin
            if (login_token != null) {
                try {
                    // build request
                    const response = await fetch(api_url + '/api/user/status', {
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
                        setUserData({
                            username: data.username,
                            roles: data.roles,
                        })
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
    }, [api_url, login_token])
    
    // show loading
    if (loading) {
        return <LoadingComponent/>
    }

    // get role color
    const text_color = user_data.roles.includes('ROLE_ADMIN') ? 'red' : 'green'

    return (
        <div className="user-navbar">
            {/* main home link */}
            <span>
                <span>➜</span>
                <Link to="/" className="sub-navigation-link">home</Link>
            </span>

            {/* upload navigation */}
            {location.pathname == '/upload' ? 
                <span>
                    <span>➜</span>
                    <Link to="/upload" className="sub-navigation-link">upload</Link> 
                </span>
            : null}

            <div className="user-data">
                <p className={`color-${text_color}`}>{user_data.username}</p>
            </div>
        </div>
    )
}
