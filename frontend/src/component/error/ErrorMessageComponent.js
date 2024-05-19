/**
 * Component custom error message
 */
export default function ErrorMessageComponent(props) {
    // error message
    let message = props.message == null ? 'Unknown error' : props.message

    // app reload
    function reload() {
        window.location.href = '/'
    }

    return (
        <div className="error-container">
            <h1 className="error-server-info">{message}</h1>
            <div className="error-buttons">
                <button type="button" onClick={reload}>reload</button>
            </div>
        </div>
    )
}
