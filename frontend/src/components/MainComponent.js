
import { useEffect, useState } from "react";
import { checkApiAvailability } from '../utils/apiUtils';

import LoginComponent from "./auth/LoginComponent";
import ApiErrorComponent from "./errors/ApiErrorComponent";
import ApiUrlSetupComponent from "./setup/ApiUrlSetupComponent";
import MaintenanceComponent from "./errors/MaintenanceComponent";
import LoadingComponent from "./sub-components/LoadingComponent";

function MainComponent() {
    const [loading, setLoading] = useState(true);
    const [api_error, setApiError] = useState(false);
    const [maintenance, setMaintenance] = useState(false);

    // get api url from local storage
    let api_url = localStorage.getItem('api-url');
    
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

                        // remove api url
                        localStorage.removeItem('api-url');

                        // reload app
                        window.location.reload();
                    }
                } catch (error) {
                    // remove api url
                    localStorage.removeItem('api-url');

                    // reload app
                    window.location.reload();
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
        
        // check is maintenance
        } else if (maintenance == true) {
            return (<MaintenanceComponent/>)
        
        // check if found api error
        } else if (api_error == true) {
            return (<ApiErrorComponent/>);

        } else {

            // show login
            return (<LoginComponent/>);
        }
    }
}

export default MainComponent;
