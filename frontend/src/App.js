import React, { useEffect, useState } from 'react'

// import config
import { APP_VERSION, DEV_MODE } from './config'

// import components
import AppRouter from './AppRouter'
import SetupComponent from './component/SetupComponent'
import AuthComponent from './component/auth/AuthComponent'
import LoadingComponent from './component/sub-component/LoadingComponent'
import ApiErrorComponent from './component/sub-component/error/ApiErrorComponent'
import ErrorMessageComponent from './component/sub-component/error/ErrorMessageComponent'

// import engal utils
import { getApiStatus, isApiAvailable } from './util/ApiUtils'

// import app styles
import './assets/css/main.css'

/**
 * Component with default app init
 */
export default function App() {
    // storage data
    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    // status states
    const [is_api_available, setApiAvailable] = useState(false)
    const [app_version, setAppVersion] = useState(null)
    const [api_error, setApiError] = useState(null)
    const [loading, setLoading] = useState(true)

    // check if api url seted
    if (api_url == null) {
        // render api setup component
        return <SetupComponent/>
    }

    // check if api available
    useEffect(() => {
        isApiAvailable(api_url)
            .then((available) => {
                setApiAvailable(available)
            })
            .catch((error) => {
                if (DEV_MODE) {
                    console.log('ERROR: ' + error)
                }
                setApiAvailable(false)
                setLoading(false)
            })
    }, [api_url])
    
    // check if response is error
    useEffect(() => {
        getApiStatus(api_url)
            .then((response_data) => {
                if (response_data.status !== 'success') {
                    setApiError(response_data.message)
                } else {
                    setAppVersion(response_data.backend_version)
                }
            })
            .catch((error) => {
                if (DEV_MODE) {
                    console.log('ERROR: ' + error)
                }
            })
            .finally(() => {
                if (login_token == null) {
                    setLoading(false)
                }
            })
    }, [api_url])

    // check user status
    useEffect(() => {
        const fetchData = async () => {
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
                    if (data.status != 'success') {
                        localStorage.removeItem('login-token')
                        window.location.reload()
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('ERROR: ' + error)
                    }
                    setApiError('Error with API connection')
                    setLoading(false)
                } finally {
                    setLoading(false)
                }
            }
        }
        fetchData()
    }, [api_url, login_token])
    
    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // handle resolution error
    if (window.innerWidth < 160 || window.innerHeight < 150) {
        return <ErrorMessageComponent message="Your screen size is not supported"/>
    }

    // handle error api connection error
    if (!is_api_available) {
        return <ApiErrorComponent/>
    }

    // handle app version error
    if (app_version != APP_VERSION) {
        return <ErrorMessageComponent message={"Your app version is not valid matchend with server, required version: " + app_version}/>
    }

    // handle api response error
    if (api_error != null) {
        return <ErrorMessageComponent message={api_error}/>
    }

    // check if user is loggedin
    if (localStorage.getItem('login-token') == null) {
        return <AuthComponent/>
    }

    // return main router component
    return <AppRouter/>
}
