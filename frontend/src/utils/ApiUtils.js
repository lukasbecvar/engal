import { appReload } from "./AppUtils";

export async function checkApiAvailability(url) {
    try {
        // make requst
        const response = await fetch(url);

        // check response
        if (response.ok) {
            const jsonResponse = await response.json();
  
            return jsonResponse.status;
        } else {
            return null;
        }
    } catch (error) {
        return null;
    }
}

export function removeApiUrl() {
    // remove api url
    localStorage.removeItem('api-url');

    // get user token from locale storage
    let user_token = localStorage.getItem('user-token');

    // remove user token if is seted
    if (user_token != null) {
        localStorage.removeItem('user-token');
    }

    appReload();
}

export function getApiUrl() {
    return localStorage.getItem('api-url');
}

export function setApiLink(api_url) {
    localStorage.setItem('api-url', api_url);
    appReload();
}
