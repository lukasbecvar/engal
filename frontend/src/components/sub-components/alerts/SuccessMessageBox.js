export default function SuccessMessageBox(props) {
    return (
        <div className='alert alert-success fade show' role='alert'>
            <strong>Notice:</strong> {props.success_message}
        </div>
    );
}
