export default function ErrorBoxComponent(props) {
    return (
        <div className='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>Error</strong> {props.error_msg}
            <button type='button' className='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    );
}
