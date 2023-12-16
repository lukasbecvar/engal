export default function SuccessMessageBox(props) {
    return (
        <div className='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>Notice</strong> {props.success_message}
            <button type='button' className='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    );
}