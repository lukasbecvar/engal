/**
 * Checks if the provided string is a valid URL.
 * @param {string} url - The URL string to be validated.
 * @returns {boolean} True if the URL is valid, otherwise false.
 */
export function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
};
