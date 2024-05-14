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
import './assets/css/scrollbar.css'

/**
 * Component with default app init
 */
export default function App() {
    // storage data
    let apiUrl = localStorage.getItem('api-url')
    let loginToken = localStorage.getItem('login-token')

    // status states
    const [isApiAvailableValue, setApiAvailable] = useState(false)
    const [appVersion, setAppVersion] = useState(null)
    const [apiError, setApiError] = useState(null)
    const [loading, setLoading] = useState(true)

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
                if (loginToken == null) {
                    setLoading(false)
                }
            })
    }, [apiUrl])

    // check user status
    useEffect(() => {
        const fetchData = async () => {
            // check if user loggedin
            if (loginToken != null) {
                try {
                    // build request
                    const response = await fetch(apiUrl + '/api/user/status', {
                        method: 'GET',
                        headers: {
                            'Accept': '*/*',
                            'Authorization': 'Bearer ' + localStorage.getItem('login-token')
                        },
                    })
    
                    // get response data
                    const data = await response.json()

                    // check if user token is valid
                    if (data.status != 'success') {
                        localStorage.removeItem('login-token')
                        window.location.reload()
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('Error to fetch user status: ' + error)
                    }

                    // remove invalid token
                    localStorage.removeItem('login-token')
                    window.location.reload()
                } finally {
                    setLoading(false)
                }
            }
        }
        fetchData()
    }, [apiUrl, loginToken])
    
    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // handle resolution error
    if (window.innerWidth < 160 || window.innerHeight < 150) {
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
        return <ErrorMessageComponent message={"Your app version is not valid matchend with server, required version: " + app_version}/>
    }

    // check if user is loggedin
    if (localStorage.getItem('login-token') == null) {
        return <AuthComponent/>
    }

    // return main router component
    return <AppRouter/>
}
