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
        <div>
            <h1>{message}</h1>
            <button type="button" onClick={reload}>reload</button>
        </div>
    )
}
