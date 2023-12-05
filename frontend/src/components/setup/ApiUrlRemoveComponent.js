// import engal utils
import { appReload } from "../../utils/AppUtils";
import { removeApiUrl } from "../../utils/ApiUtils";

export default function ApiUrlRemoveComponent() {
    // get api url form locale storage
    let api_url = localStorage.getItem('api-url');
    
    function resetUrl() {
        removeApiUrl();
    }

    function reload() {
        appReload();
    }

    return (
        <div className='component d-flex align-items-center justify-content-center vh-100'>
            <div className='text-center'>
                <p className='fs-3'><span className='text-danger'>Opps!</span></p>
                <p className='lead text-light'>
                    Error to connect API: {api_url}
                </p>
                <a href='#' className='btn btn-outline-info' onClick={resetUrl}>Set new URL</a>
                <a href='#' className='btn btn-outline-info ml-3' onClick={reload}>Reload</a>
            </div>
        </div>
    );
}
