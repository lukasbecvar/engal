/**
 * Component API error
 */
export default function ApiErrorComponent() {
    // storage data
    let apiUrl = localStorage.getItem('api-url')

    // app reload
    function reload() {
        window.location.href = '/'
    }

    // reset api url
    function removeApiUrl() {
        localStorage.removeItem('api-url')
        localStorage.removeItem('login-token')
        window.location.href = '/'
    }

    return (
        <div className="error-container">
            <p className="error-message">API connection error</p>
            <p className="error-server-info">
                Your API server ({apiUrl}) is unreachable <br/>
                Please wait and try again later, or you can set up a different API server
            </p>

            <div className="error-buttons">
                <button type="button" onClick={reload}>reload</button>
                <button type="button" onClick={removeApiUrl}>Set new API</button>
            </div>
        </div>
    )
}
