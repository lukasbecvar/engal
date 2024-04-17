import React, { useEffect, useState } from 'react'
import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'

// import config
import { APP_VERSION } from './config'

// import components
import SetupComponent from './component/SetupComponent'
import DashboardComponent from './component/DashboardComponent'
import NotFoundComponent from './component/sub-component/NotFoundComponent'
import LoadingComponent from './component/sub-component/LoadingComponent'
import ErrorMessageComponent from './component/sub-component/ErrorMessageComponent'

// import engal utils
import { getApiUrl } from './util/StorageUtil'
import { getApiStatus, isApiAvailable } from './util/ApiUtils'

// import app style
import './assets/css/main.css'

export default function App() {
    const [is_api_available, setApiAvailable] = useState(false)
    const [app_version, setAppVersion] = useState(null)
    const [api_error, setApiError] = useState(null)
    const [loading, setLoading] = useState(true)

    // get api url from local storage
    let api_url = getApiUrl()
    
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
                setLoading(false)
            });
    }, [api_url])

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

    // render component by route
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<DashboardComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
