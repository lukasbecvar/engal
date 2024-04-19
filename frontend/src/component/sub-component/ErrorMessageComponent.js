export default function ErrorMessageComponent(props) {
    let message = props.message == null ? 'Unknown error' : props.message


    function reload() {
        window.location.reload()
    }

    return (
        <div>
            <h1>{message}</h1>

            <button type="button" onClick={reload}>reload</button>
        </div>
    )
}
