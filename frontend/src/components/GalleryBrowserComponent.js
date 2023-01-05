// react
import { useState } from "react";

// app components
import LoadingComponent from "./subcomponents/LoadingComponent.js"
import ImageComponent from "./subcomponents/ImageComponent.js";

// application config
import {API_URL, API_TOKEN, MAX_ITEMS_PER_PAGE, ENCRYPTION_TOKEN} from "../config.js"

// light gallery
import LightGallery from 'lightgallery/react';
 
// light gallery styles
import 'lightgallery/css/lightgallery.css';
import 'lightgallery/css/lg-zoom.css';
import 'lightgallery/css/lg-fullscreen.css';
import 'lightgallery/css/lg-thumbnail.css';
import 'lightgallery/css/lg-autoplay.css';
 
// light gallery plugins
import lgZoom from 'lightgallery/plugins/zoom';
import lgFullscreen from 'lightgallery/plugins/fullscreen';
import lgAutoplay from 'lightgallery/plugins/autoplay';


import CryptoJS from 'crypto-js'


const GalleryBrowserComponent = () => {

    // get url parameters
    let urlParams = new URLSearchParams(window.location.search);

    // get page system values
    let startBy = parseInt(urlParams.get('startby'));

    // get gallery name paramater
    let galleryName = urlParams.get("gallery");

    ///////////////////////////////////////////////////////////////////////
    const [imagesList, setImagesList] = useState(null)
    const [canGetList, setCanGetList] = useState(true)

    const setImagesListByAPI = async () => {

        const response = await fetch(API_URL + "?token=" + API_TOKEN + "&action=getAllImagesDataByGallery&galleryName=" + galleryName + "&limit=" + MAX_ITEMS_PER_PAGE + "&startBy=" + startBy)

        const data = await response.json() 

        setImagesList(data)

        setCanGetList(false)
    }

    if (canGetList) {
        setImagesListByAPI()
    }
    ///////////////////////////////////////////////////////////////////////


    if (imagesList != null) {

        const imagesContent = [];
        const numlen = imagesList.length

        let imagesCounter = 0

        for (let i = 0; i < numlen; i++) {
            
            // get image id
            let imageID = imagesList[i].id

            let image = "data:image/jpg;base64," + imagesList[i].content

            // decrypt image
            if (ENCRYPTION_TOKEN != null) {
                image = "data:image/jpg;base64," + CryptoJS.AES.decrypt(imagesList[i].content, ENCRYPTION_TOKEN).toString(CryptoJS.enc.Utf8)
            }

            // get image name
            let imageName = imagesList[i].name

            // decrypt image name
            if (ENCRYPTION_TOKEN != null) {
                imageName = CryptoJS.AES.decrypt(imagesList[i].name, ENCRYPTION_TOKEN).toString(CryptoJS.enc.Utf8)
            }

            // get image gallery name
            let imageGallery = imagesList[i].gallery 
            let imageGalleryLink =  "<a key='gallery-button' href='/browse/?gallery=" + imagesList[i].gallery + "&startby=0'>" + imagesList[i].gallery + "</a>"
            let editButton = "<a key='edit-button' href='/edit/?id=" + imageID + "'>Edit</a>"
            let deleteButton = "<a key='delete-button' href='/delete/?id=" + imageID + "&gallery=" + imageGallery + "'>Delete</a>"

            // push image data to new image component
            imagesContent.push(                
                <ImageComponent key={i + "-image"} 
                    imageID={imageID}
                    image={image} 
                    imageName={imageName} 
                    imageGalleryLink={imageGalleryLink} 
                    editButton={editButton} 
                    deleteButton={deleteButton}>    
                </ImageComponent>
            )

            imagesCounter++;
        }

        const pagerButtons = []

        // pager buttons value builders
        let nextPage = Number(startBy) + MAX_ITEMS_PER_PAGE;
        let backPage = Number(startBy) - MAX_ITEMS_PER_PAGE

        // check if gallery not random
        if (galleryName != "randomImagess2WH92Aww") {

            if (startBy != "0") {
                pagerButtons.push(<a key="back-button" className="page-button" href={"/browse/?gallery=" + galleryName + "&startby=" + backPage}>Back</a>)
            }
    
            if (imagesCounter == MAX_ITEMS_PER_PAGE) {
                pagerButtons.push(<a key="back-next" className="page-button" href={"/browse/?gallery=" + galleryName + "&startby=" + nextPage}>Next</a>)
            }
        } else {
            pagerButtons.push(<center><a key="refresh" className="page-button" href={"/browse/?gallery=" + galleryName + "&startby=0"}>Refresh</a></center>)
        }

        // return gallery box
        return (
            <div className="gallery-box">
                <LightGallery plugins={[lgFullscreen, lgZoom, lgAutoplay]}>
                    {imagesContent}
                </LightGallery>
                <div className="page-button-box">
                    {pagerButtons}
                </div>
            </div>
        )
    } else {
        return (<LoadingComponent/>)
    }

}

export default GalleryBrowserComponent
