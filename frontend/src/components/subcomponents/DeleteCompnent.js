// react import
import { Link } from "react-router-dom";

// application config
import {API_URL, API_TOKEN} from "../../config.js"


const DeleteCompnent = () => {
    
    // get url parameters
    let urlParams = new URLSearchParams(window.location.search);

    // get imageID name paramater
    let imageID = urlParams.get("id");

    // get gallery name for return to galley
    let imageGallery = urlParams.get("gallery");

    // redirect back to gallery
    const handleDelteClick = event => {
        setTimeout(() => {
            window.location.replace("/browse/?gallery=" + imageGallery + "&startby=0");
        }, 500);
    }

    // return delete from
    return (
        <div className="delete-form">
            <p className="delete-title">You want delete image: {imageID} from {imageGallery}</p>
            <div className="delete-buttons">
                <a className="delete-button" onClick={handleDelteClick} target="_blank" rel="noreferrer" href={API_URL + "?token=" + API_TOKEN + "&action=delete&id=" + imageID}>Yes</a>
                <Link className="delete-button" to={"/browse/?gallery=" + imageGallery + "&startby=0"}>No</Link>
            </div>
        </div>
    )
}

export default DeleteCompnent