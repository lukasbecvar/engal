export default function MaintenanceComponent() {
    return (
        <div className='component d-flex align-items-center justify-content-center vh-100'>
            <div className='text-center'>
                <p className='fs-3'><span className='text-danger'>Opps!</span></p>
                <p className='lead text-light'>
                    The service is temporarily unavailable due to maintenance
                </p>
                <a href='/' className='btn btn-outline-info'>Reload</a>
            </div>
        </div>
    );
}
