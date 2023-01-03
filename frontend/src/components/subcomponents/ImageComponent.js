const ImageComponent = (props) => {

    // return image component
    return (
        <a key={props.imageID + "-link"} href={props.image}>
            <img key={props.imageID} className="image-item" alt={props.imageName + " | " + props.imageGalleryLink + " " + props.editButton + " " + props.deleteButton} src={props.image}/>
        </a>
    )
}

export default ImageComponent