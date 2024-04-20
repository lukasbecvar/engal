import { useEffect, useState } from "react"
import { Link } from 'react-router-dom'

// engal components
import LoadingComponent from "./sub-component/LoadingComponent"
import ErrorMessageComponent from "./sub-component/ErrorMessageComponent"

// engal utils
import { DEV_MODE } from "../config"

/**
 * Component main app (user) dashboard
 */
export default function DashboardComponent() {
    // storage data
    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

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

    return (
        <div>
            <div>
                <p>Engal</p>
                <p>user: {user_data.username}</p>
                <p>role: {user_data.roles}</p>
                <Link to='/logout'>logout</Link>
            </div>

            <p>! app dashboard !</p>
        </div>
    )
}
