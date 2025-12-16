/**
 * Checks if a given URL is valid
 * 
 * @param {string} url - The URL to validate
 * @returns {boolean} - True if the URL is valid, false otherwise
 */
export function isValidUrl(url) {
    try {
        new URL(url)
        return true
    } catch (error) {
        return false
    }
}

/**
 * Normalizes API URL by trimming whitespace and removing trailing slash
 * 
 * @param {string} url
 * @returns {string}
 */
export function normalizeUrl(url) {
    if (!url) return ''
    const trimmed = url.trim()
    if (trimmed.endsWith('/')) {
        return trimmed.replace(/\/+$/, '')
    }
    return trimmed
}
