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

export async function getApiStatus(url) {
    try {
        const response = await fetch(url);
        
        if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
            console.log("api connection error: content-type is not json");
            return {
                status: 'error',
                message: 'Unknown error',
                backend_version: null
            };
        } else {
            const data = await response.json();
        
            if (data.status === "success") {
                return {
                    status: 'success',
                    message: data.message,
                    backend_version: data.backend_version
                };
            } else {
                return {
                    status: 'error',
                    message: data.message,
                    backend_version: null
                };
            }    
        }
    } catch (error) {
        console.log("api connection error: Unknown error");
        return {
            status: 'error',
            message: 'Unknown error',
            backend_version: null
        };
    }
}
