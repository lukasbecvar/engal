/**
 * Checks the availability of an API at the given URL.
 * 
 * @param {string} url - The URL of the API.
 * @returns {boolean} - Returns true if the API is available, false otherwise.
 */
export async function isApiAvailable(url) {
    try {
        const response = await fetch(url, { method: 'HEAD' })
        if (response.ok) {
            return true
        } else {
            return false
        }
    } catch (error) {
        return false
    }
}

/**
 * Retrieves the status and message from the API at the given URL.
 * 
 * @param {string} url - The URL of the API.
 * @returns {Object} - Returns an object containing the status and message from the API.
 *                     The status can be 'success' or 'error'.
 *                     If an error occurs during the request, the status will be 'error'.
 *                     The message will contain the response message from the API.
 *                     If an error occurs, it will be logged to the console.
 */
export async function getApiStatus(url) {
    try {
        const response = await fetch(url)
        const data = await response.json()
        
        if (data.status === "success") {
            return {
                status: 'success',
                message: data.message,
                backend_version: data.backend_version
            }
        } else {
            return {
                status: 'error',
                message: data.message,
                backend_version: null
            }
        }
    } catch (error) {
        console.error("api connection error:", error)
    }
}

