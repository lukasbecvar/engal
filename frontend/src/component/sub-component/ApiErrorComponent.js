export default function ApiErrorComponent() {

    let api_url = localStorage.getItem('api-url')

    function reload() {
        window.location.href = '/'
    }

    function removeApiUrl() {
        localStorage.removeItem('api-url')
        localStorage.removeItem('login-token')
        window.location.href = '/'
    }

    return (
        <div>
            <p>API connection error</p>
            <p>Your API server ({api_url}) is unreachable</p>
            <p>Please wait and try again later, or you can set up a different API server</p>

            <button type="button" onClick={reload}>reload</button>
            <button type="button" onClick={removeApiUrl}>Set new API</button>
        </div>
    )
}
