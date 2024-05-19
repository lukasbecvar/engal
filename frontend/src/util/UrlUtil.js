/**
 * Checks if a given URL is valid.
 * @param {string} url - The URL to validate.
 * @returns {boolean} - True if the URL is valid, false otherwise.
 */
export function isValidUrl(url) {
    try {
        new URL(url)
        return true
    } catch (error) {
        return false
    }
}
