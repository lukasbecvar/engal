import NavigationComponent from "../sub-components/NavigationComponent"

export default function RegisterDisabledComponent() {
    return (
        <div className='component'>

            <NavigationComponent/>
            
            <div className='text-center'>
                <div className='container mt-5'>
                    <p className='fs-3'><span className='text-danger'>Opps!</span></p>
                    <h5 className='text-light'>
                        We are sorry new registrations is currently disabled.
                    </h5>
                </div>
            </div>        
        </div>
    );
}
