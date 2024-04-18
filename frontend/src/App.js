import React, { useEffect, useState } from 'react'

// import config
import { APP_VERSION } from './config'

// import components
import SetupComponent from './component/SetupComponent'
import LoadingComponent from './component/sub-component/LoadingComponent'
import ErrorMessageComponent from './component/sub-component/ErrorMessageComponent'

// import engal utils
import { getApiStatus, isApiAvailable } from './util/ApiUtils'

// import app style
import './assets/css/main.css'
import { AppRouter } from './AppRouter'
import LoginComponent from './component/auth/LoginComponent'

export default function App() {
    const [is_api_available, setApiAvailable] = useState(false)
    const [app_version, setAppVersion] = useState(null)
    const [api_error, setApiError] = useState(null)
    const [loading, setLoading] = useState(true)

    // get api url from local storage
    let api_url = localStorage.getItem('api-url')

    // get login token from local storage
    let login_token = localStorage.getItem('login-token')

    // check if api url seted
    if (api_url == null) {
        // render api setup component
        return <SetupComponent/>
    }

    // check if api available
    useEffect(() => {
        isApiAvailable(api_url)
            .then((available) => {
                setApiAvailable(available);
            })
            .catch((error) => {
                console.log('api connection error: ' + error)
                setApiAvailable(false)
                setLoading(false)
            })
    }, [api_url]);
    
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
                console.error('Error:', error)
            })
            .finally(() => {
                if (login_token == null) {
                    setLoading(false)
                }
            });
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
                    });
    
                    // get response data
                    const data = await response.json();
                    
                    // check if user tokne is valid
                    if (data.status != 'success') {
                        localStorage.removeItem('login-token')
                        window.location.reload()
                    }
                } catch (error) {
                    setApiError('Error with API connection')
                    setLoading(false)
                } finally {
                    setLoading(false)
                }
            }
        };
    
        fetchData();
    }, [api_url]);
    
    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // handle error api connection error
    if (!is_api_available) {
        return <ErrorMessageComponent message="API connection error"/>
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
        return <LoginComponent/>
    }

    // return main router component
    return <AppRouter/>
}
