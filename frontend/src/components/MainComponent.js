function MainComponent() {

    let api_url = localStorage.getItem('api-url');

    let user_token = localStorage.getItem('user-token');

    async function logout() {

        try {
            const formData = new FormData();
            formData.append('token', user_token);

            const response = await fetch(api_url + '/logout', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                // Ošetření odmítnuté odpovědi (HTTP status není OK)
                console.error('Error:', response.status);
                return;
            }


            if (user_token != null) {
                localStorage.removeItem('user-token'); 
                window.location.reload();           
            }

        } catch (error) {
            // Ošetření chyb při samotném HTTP požadavku
            console.error('Error:', error);
        }

    }

    return (
        <div>
            <p>logged: {localStorage.getItem('user-token')}</p>
            <button type="button" onClick={logout}>Logout</button>
        </div>
    );
}

export default MainComponent;
