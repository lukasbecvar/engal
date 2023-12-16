import { appReload } from "../../utils/AppUtils";

export default function CustomErrorComponent(props) {
    return (
        <div className='component d-flex align-items-center justify-content-center vh-100'>
            <div className='text-center'>
                <p className='fs-3'><span className='text-danger'>Opps!</span></p>
                <p className='lead text-light'>
                    {props.error_message}
                </p>
                <button className='btn btn-outline-info' onClick={appReload}>Reload</button>
            </div>
        </div>
    );
}
