import { useEffect, useState } from "react";
import { checkApiAvailability } from '../utils/ApiUtils';

import LoginComponent from "./auth/LoginComponent";
import ApiErrorComponent from "./errors/ApiErrorComponent";
import ApiUrlSetupComponent from "./setup/ApiUrlSetupComponent";
import MaintenanceComponent from "./errors/MaintenanceComponent";
import LoadingComponent from "./sub-components/LoadingComponent";
import ApiUrlRemoveComponent from "./setup/ApiUrlRemoveComponent";
import MainComponent from "./MainComponent";

function InitComponent() {
    const [loading, setLoading] = useState(true);
    const [api_error, setApiError] = useState(false);
    const [api_connction_error, setApiConnectionError] = useState(false);
    const [maintenance, setMaintenance] = useState(false);

    // get api url from local storage
    let api_url = localStorage.getItem('api-url');
    
    let user_token = localStorage.getItem('user-token');

    // check if api is reachable
    useEffect(() => {
        const checkAPI = async () => {
            if (api_url != null) {
                try {
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
                    setApiConnectionError(true);
                }
            }
        };

        // check api
        checkAPI();

        // disable loading
        setLoading(false);
    }, [api_url]);

    // show loading
    if (loading == true) {
        return (<LoadingComponent/>);
    } else {

        // check if api url not seted
        if (api_url == null || api_url === '') {
            return (<ApiUrlSetupComponent/>);
        
        // check if api connection error found
        } else if (api_connction_error == true) {
            return (<ApiUrlRemoveComponent/>);

        // check is maintenance
        } else if (maintenance == true) {
            return (<MaintenanceComponent/>)
        
        // check if found api error
        } else if (api_error == true) {
            return (<ApiErrorComponent/>);

        } else {

            if (user_token == null) {

                // show login
                return (<LoginComponent/>);
            } else {
                return (<MainComponent/>);
            }
        }
    }
}

export default InitComponent;
