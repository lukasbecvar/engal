export default function ErrorBoxComponent(props) {
    return (
        <div className='alert alert-danger fade show' role='alert'>
            <strong>Error:</strong> {props.error_msg}
        </div>
    );
}
