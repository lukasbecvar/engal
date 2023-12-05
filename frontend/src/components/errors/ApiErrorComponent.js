import { appReload } from "../../utils/AppUtils";

export default function ApiErrorComponent() {
    return (
        <div className='component d-flex align-items-center justify-content-center vh-100'>
            <div className='text-center'>
                <p className='fs-3'><span className='text-danger'>Opps!</span></p>
                <p className='lead text-light'>
                    Unknown api error, please contact your administroator
                </p>
                <button className='btn btn-outline-info' onClick={appReload}>Reload</button>
            </div>
        </div>
    );
}
