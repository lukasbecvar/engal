export default function WarningMessageBox(props) {
    return (
        <div className='alert alert-warning fade show' role='alert'>
            <strong>Warning</strong> {props.warning_message}
        </div>
    );
}
