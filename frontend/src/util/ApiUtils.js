// engal utils
import { DEV_MODE } from "../config"

/**
 * Checks if the API at the specified URL is available.
 * @param {string} url - The URL of the API.
 * @returns {boolean} - True if the API is available, false otherwise.
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
 * Retrieves the status of the API at the specified URL.
 * @param {string} url - The URL of the API.
 * @returns {Object} - An object containing the status, message, and backend version of the API.
 */
export async function getApiStatus(url) {
    try {
        const response = await fetch(url)
        
        // check if the response is OK and the content type is JSON
        if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
            if (DEV_MODE) {
                console.log("API connection error: Unknown error")
            }
            return {
                status: 'error',
                message: 'Unknown error',
                backend_version: null
            }
        } else {
            const data = await response.json()
        
            // check if the response contains the required fields
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
        }
    } catch (error) {
        if (DEV_MODE) {
            console.log("API connection error: Unknown error")
        }
        return {
            status: 'error',
            message: 'Unknown error',
            backend_version: null
        }
    }
}
