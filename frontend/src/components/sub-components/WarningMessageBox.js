export default function WarningMessageBox(props) {
    return (
        <div className='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong>Warning</strong> {props.warning_message}
            <button type='button' className='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    );
}
