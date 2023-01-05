// react
import { useState } from "react";

// application config
import {API_URL, API_TOKEN, ENCRYPTION_TOKEN} from "../config.js"

// libs
import $ from 'jquery'; 

// app components
import LoadingComponent from "./subcomponents/LoadingComponent.js"

import CryptoJS from 'crypto-js'

const UploadComponent = () => {

    // info box use state
    const [info, setInfo] = useState("")


    
    // upload function
    const upload = () => {

            // get from data
            let imgName = document.getElementById("name").value
            let imgGallery = document.getElementById("gallery").value
            let imgContent = document.getElementById("content")

            // set image name if empty
            if (imgName.length === 0) {
                imgName = "image"
            }

            // check if data is not empty
            if (imgName.length === 0) {
            
                // set info
                setInfo(<p className="info-text">Error: image name is empty!</p>)

            } else if (imgGallery.length === 0) {
                

                // set info
                setInfo(<p className="info-text">Error: gallery is empty!</p>)

            } else if (imgContent.value.length === 0) {
            
                // set info
                setInfo(<p className="info-text">Error: file is empty!</p>)
            
            // if data valid
            } else {

                // get image
                let file = imgContent.files[0];
                let imageType = /image.*/;  

                // check if file is image
                if (file.type.match(imageType)) { 

                    // init reader
                    const reader = new FileReader();

                    // check event
                    reader.addEventListener("load", function () {

                        // split data type
                        let base64 = reader.result.split(',')[1]

                        // init gallery name
                        let imgFinalGallery = imgGallery
                        
                        // get image content
                        let imageContent = base64

                        // encrypt image
                        if (ENCRYPTION_TOKEN != null) {
                            imageContent = CryptoJS.AES.encrypt(base64, ENCRYPTION_TOKEN).toString()
                        }

                        // get img name
                        let imageName = imgName

                        // encrypt image name
                        if (ENCRYPTION_TOKEN != null) {
                            imageName = CryptoJS.AES.encrypt(imgName, ENCRYPTION_TOKEN).toString()
                        }

                        // send upload request with jquery
                        $.ajax({
                            type: "POST",
                            url: API_URL + '?token=' + API_TOKEN + '&action=upload',
                            data: {"name": imageName, "gallery": imgFinalGallery, "content": imageContent},
                            error: successUpload(imgName)
                        });
                    });

                    // init reader function
                    reader.readAsDataURL(file);

                } else { 
                    // set info msg
                    setInfo(<p className="info-text">File not supported!</p>)
                }
            }

    }

    // show success msg
    const successUpload = () => {

        // get image name
        let imgName = document.getElementById("name").value

        // set info msg
        setInfo(<p className="info-text">{imgName}: uploaded!</p>)
    }

    // gallery list use state
    const [galleryList, setGalleryList] = useState(null)
    const [canGetList, setCanGetList] = useState(true)

    // get gallery list
    const setGalleryListByAPI = async () => {

        const response = await fetch(API_URL + "?token=" + API_TOKEN + "&action=allGaleryNames")

        const data = await response.json() 

        setGalleryList(data)

        setCanGetList(false)
    }

    if (canGetList) {
        setGalleryListByAPI()
    }

    // return upload form
    if (galleryList != null) {
        return (
            <div className="uploader">
                <div className="info-box">{info}</div>
                <div className="uploader-form">
                    <p className="uploader-title">image upload</p>
                    <input className="text-input" id="name" type="text" name="name" placeholder="name"/><br/>
                    <input className="text-input" id="gallery" type="text" name="gallery" list="galleries" placeholder="gallery"/><br/>
                    <datalist id="galleries">
                        {galleryList.map(name => <option key={name}>{name}</option>)}
                    </datalist>
                    <input className="file-input" type="file" id="content" name="filename"/><br/>
                    <button className="submit-button" onClick={upload}>Upload</button>
                </div>
            </div>
        )
    } else {
        return (<LoadingComponent/>)
    }

}

export default UploadComponent 