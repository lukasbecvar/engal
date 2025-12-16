import React, { useEffect, useState } from 'react'

// import config
import { APP_VERSION, DEV_MODE } from './config'

// import components
import AppRouter from './AppRouter'
import SetupComponent from './component/SetupComponent'
import AuthComponent from './component/auth/AuthComponent'
import ApiErrorComponent from './component/error/ApiErrorComponent'
import LoadingComponent from './component/sub-component/LoadingComponent'
import ErrorMessageComponent from './component/error/ErrorMessageComponent'

// import engal utils
import { getApiStatus, isApiAvailable } from './util/ApiUtils'

// import app styles
import './assets/css/main.css'
import './assets/css/scrollbar.css'

/**
 * Component with default app init
 */
export default function App() {
    // storage data
    let apiUrl = localStorage.getItem('api-url')

    // status states
    const [viewportTooSmall, setViewportTooSmall] = useState(window.innerWidth < 210 || window.innerHeight < 150)
    const [isApiAvailableValue, setApiAvailable] = useState(false)
    const [appVersion, setAppVersion] = useState(null)
    const [apiError, setApiError] = useState(null)
    const [loading, setLoading] = useState(true)
    const [isAuthenticated, setAuthenticated] = useState(false)

    // check if api url seted
    if (apiUrl == null) {
        // render api setup component
        return <SetupComponent/>
    }

    // check if api available
    useEffect(() => {
        isApiAvailable(apiUrl)
            .then((available) => {
                setApiAvailable(available)
            })
            .catch((error) => {
                if (DEV_MODE) {
                    console.log('Error api init request: ' + error)
                }
                setApiAvailable(false)
                setLoading(false)
            })
    }, [apiUrl])
    
    // check if response is error
    useEffect(() => {
        getApiStatus(apiUrl)
            .then((responseData) => {
                if (responseData.status !== 'success') {
                    if (responseData.message == 'Engal API is in maintenance mode') {
                        setApiError('Engal API is in maintenance mode')
                    } else {
                        setApiError(responseData.message)
                    }
                } else {
                    setAppVersion(responseData.backend_version)
                }
            })
            .catch((error) => {
                if (DEV_MODE) {
                    console.log('Error api init request: ' + error)
                }
            })
            .finally(() => {
                setLoading(false)
            })
    }, [apiUrl])

    // check user status
    useEffect(() => {
        const fetchData = async () => {
            // check if user loggedin
            try {
                // build request
                const response = await fetch(apiUrl + '/api/user/status', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Accept': '*/*',
                    },
                })

                // get response data
                const data = await response.json()

                // check if user token is valid
                setAuthenticated(data.status === 'success')
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error to fetch user status: ' + error)
                }
                setAuthenticated(false)
            } finally {
                setLoading(false)
            }
        }
        fetchData()
    }, [apiUrl])

    // listen to viewport resize to update support check
    useEffect(() => {
        const handleResize = () => {
            setViewportTooSmall(window.innerWidth < 210 || window.innerHeight < 150)
        }
        window.addEventListener('resize', handleResize)
        return () => window.removeEventListener('resize', handleResize)
    }, [])
    
    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // handle resolution error
    if (viewportTooSmall) {
        return <ErrorMessageComponent message="Your screen size is not supported"/>
    }

    // handle error api connection error
    if (!isApiAvailableValue) {
        return <ApiErrorComponent/>
    }

    // handle api response error
    if (apiError != null) {
        return <ErrorMessageComponent message={apiError}/>
    }

    // handle app version error
    if (appVersion != APP_VERSION) {
        return <ErrorMessageComponent message={"Your app version is not valid matchend with server, required version: " + appVersion}/>
    }

    // check if user is loggedin
    if (!isAuthenticated) {
        return <AuthComponent/>
    }

    // return main router component
    return <AppRouter/>
}
