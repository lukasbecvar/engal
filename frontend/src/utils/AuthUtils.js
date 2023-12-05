import { appReload } from "./AppUtils";

export function userLogin(user_token) {
    localStorage.setItem('user-token', user_token);
    window.location.reload();
}

export function getUserToken() {
    return localStorage.getItem('user-token');
}

export function userLogout() {
    localStorage.removeItem('user-token');
    appReload();
}
