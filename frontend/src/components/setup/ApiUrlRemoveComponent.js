function ApiUrlRemoveComponent() {
    
    let api_url = localStorage.getItem('api-url');
    let user_token = localStorage.getItem('user-token');
    
    function reload() {
        // reload app
        window.location.reload();
    }

    function removeApiUrl() {
        // remove api url
        localStorage.removeItem('api-url');

        if (user_token != null) {
            localStorage.removeItem('user-token');
        }

        reload();
    }

    return (
        <div>
            <p>Error to connect API: {api_url}</p>
            <p>You can set new api url or try again later</p>
            <button type="button" onClick={removeApiUrl}>Set URL</button>
            <button type="button" onClick={reload}>Reload</button>
        </div>
    );
}

export default ApiUrlRemoveComponent;
