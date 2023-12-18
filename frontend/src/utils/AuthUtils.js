import { appReload } from "./AppUtils";

// login user (store user token to session)
export function userLogin(user_token) {
    localStorage.setItem('user-token', user_token);
    window.location.reload();
}

// get user token from storage
export function getUserToken() {
    return localStorage.getItem('user-token');
}

// remove user token from storage
export function userLogout() {
    localStorage.removeItem('user-token');
    appReload();
}
