function ApiUrlRemoveComponent() {
    
    let api_url = localStorage.getItem('api-url');
    
    function reload() {
        // reload app
        window.location.reload();
    }

    function removeApiUrl() {
        // remove api url
        localStorage.removeItem('api-url');

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
