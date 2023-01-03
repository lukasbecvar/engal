// react 
import { useState } from "react";
import { Link } from "react-router-dom";

// app components
import LoadingComponent from "./LoadingComponent.js"
 
// application config
import {API_URL, API_TOKEN,} from "../config.js"

const GalleryListComponent = () => {

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

    // counter use state
    const [photosCount, setPhotosCount] = useState("...")
    const [galleryCount, setGalleryCount] = useState("...")
    const [canGetCount, setCanGetCount] = useState(true)

    // get counters
    const setCountByAPI = async () => {

        const response = await fetch(API_URL + "?token=" + API_TOKEN + "&action=counter")

        const data = await response.json() 

        setPhotosCount(data.images)
        setGalleryCount(data.galleries)

        setCanGetCount(false)
    }

    if (canGetCount) {
        setCountByAPI()
    }

    // return main gallery selector
    if (galleryList != null) { 
        return (
            <div className="main-selectro">
                <div className="stats-bar">Photo:{photosCount}, Galleries:{galleryCount}</div>
                <div className="gallery-list">
                    <div className="static-gallery-link">
                        <li key="all-images"><Link className="gallery-link" to="/browse/?gallery=allhPC12fR0u&startby=0">All images</Link></li>
                        <li key="random-images"><Link className="gallery-link" to="/browse/?gallery=randomImagess2WH92Aww&startby=0">random</Link></li>
                    </div>
                    <div className="gallery-links">
                        {galleryList.map(name => <li key={name}><Link className="gallery-link" to={"/browse/?gallery="+name+"&startby=0"}>{name}</Link></li>)}
                    </div>
                </div>
            </div>
        )
    } else {
        return (<LoadingComponent/>)
    }   
}
 
export default GalleryListComponent