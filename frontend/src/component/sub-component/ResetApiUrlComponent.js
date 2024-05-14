// engal utils
import { DEV_MODE } from "../../config";

/*
 * Reset api access point url component
*/
export default function ResetApiUrlComponent() {
    try {
        localStorage.removeItem('api-url')
    } catch(error) {
        if (DEV_MODE) {
            console.log('Error to remove api url: ' + error)
        }
    } finally {
        window.location.href = '/';
    }
}