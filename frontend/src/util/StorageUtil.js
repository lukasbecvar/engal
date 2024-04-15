/**
 * Retrieves the API URL from the localStorage.
 * @returns {string|null} The API URL if it exists in the localStorage, otherwise null.
 */
export function getApiUrl() {
    return localStorage.getItem('api-url')
}

/**
 * Sets the API URL in the localStorage and reloads the window.
 * @param {string} url - The API URL to be stored.
 */
export function setApiUrlStorage(url) {
    localStorage.setItem('api-url', url)
    window.location.reload()
}

/**
 * Deletes the API URL from the localStorage.
 */
export function deleteApiUrlFormStorage() {
    localStorage.removeItem('api-url')
}
