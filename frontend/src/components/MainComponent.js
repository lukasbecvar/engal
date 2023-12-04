import { useEffect, useState } from "react";
import LoadingComponent from "./sub-components/LoadingComponent";

function MainComponent() {

    const [loading, setLoading] = useState(true);

    const [username, setUsername] = useState(null);

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
    
    useEffect(() => {
        async function fetchData() {
            if (username == null) {
                try {
                    const formData = new FormData();
                    formData.append('token', user_token);
        
                    const response = await fetch(api_url + '/user/status', {
                        method: 'POST',
                        body: formData
                    });
        
                    if (!response.ok) {
                        // Ošetření odmítnuté odpovědi (HTTP status není OK)
                        console.error('Error:', response.status);
                        return;
                    }
                    const data = await response.json();
        
                    if (data.status === 'success') {
                        setUsername(data.username);
                    }
        
                } catch (error) {
                    // Ošetření chyb při samotném HTTP požadavku
                    console.error('Error:', error);
                }
            }
            setLoading(false);
        }
    
        fetchData(); // Okamžité volání asynchronní funkce
    
    }, []);

    // show loading
    if (loading == true) {
        return (<LoadingComponent/>);
    } else {
        return (
            <div>
                <p>logged: {localStorage.getItem('user-token')}, user: {username}</p>
                <button type="button" onClick={logout}>Logout</button>
            </div>
        );
    }
}

export default MainComponent;
