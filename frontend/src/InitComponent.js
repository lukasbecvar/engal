import { useEffect, useState } from "react";

// import config values
import { DEV_MODE } from "./config";

// import engal utils
import { getUserToken } from "./utils/AuthUtils";
import { checkApiAvailability, getApiUrl } from './utils/ApiUtils';

// import engal components
import MainComponent from "./components/MainComponent";
import LoginComponent from "./components/auth/LoginComponent";
import ApiErrorComponent from "./components/errors/ApiErrorComponent";
import ApiUrlSetupComponent from "./components/setup/ApiUrlSetupComponent";
import MaintenanceComponent from "./components/errors/MaintenanceComponent";
import LoadingComponent from "./components/sub-components/LoadingComponent";
import ApiUrlRemoveComponent from "./components/setup/ApiUrlRemoveComponent";

export default function InitComponent() 
{
    // get api url from local storage
    let api_url = getApiUrl();

    // state variables for managing component state
    const [loading, setLoading] = useState(true);
    const [api_error, setApiError] = useState(false);
    const [maintenance, setMaintenance] = useState(false);
    const [api_connction_error, setApiConnectionError] = useState(false);

    // check if api is reachable
    useEffect(function() {
        async function checkAPI() {
            if (api_url !== null) {
                try {
                    // check api status
                    const result = await checkApiAvailability(api_url);
      
                    // check if maintenance enabled
                    if (result === 'maintenance') {
                        setMaintenance(true);
                    }

                    // check if error found
                    if (result === 'error') {
                        setApiError(true);
                    }

                    // check if api is unreachable
                    if (result === null) {
                        setApiConnectionError(true);
                    }
                } catch (error) {
                    if (DEV_MODE) {
                        console.log('Error: ' + error);
                    }
                    setApiConnectionError(true);
                }
            }
        }

        // check api
        checkAPI();

        // disable loading
        setLoading(false);
    }, [api_url])

    // show loading
    if (loading === true) {
        return <LoadingComponent/>;
    } else {

        // check if api url not seted
        if (api_url == null || api_url === '') {
            return <ApiUrlSetupComponent/>;
        
        // check if api connection error found
        } else if (api_connction_error === true) {
            return <ApiUrlRemoveComponent/>;

        // check is maintenance
        } else if (maintenance === true) {
            return <MaintenanceComponent/>;
        
        // check if found api error
        } else if (api_error === true) {
            return <ApiErrorComponent/>;

        } else {

            // check if user not logged
            if (getUserToken() === null) {

                // show login
                return <LoginComponent/>;
            } else {

                // init main componnt
                return <MainComponent/>;
            }
        }
    }
}
