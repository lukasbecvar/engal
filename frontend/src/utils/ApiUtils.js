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
