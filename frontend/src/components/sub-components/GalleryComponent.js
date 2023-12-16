export default function GalleryComponent(props) {
    return (
        <div className="gallery-link" id="gallery2">
            <span className="link-text">
                <img className="image-link" src={'data:image/jpg;base64,' + props.thumbnail} alt={props.name}/>
                <center>{props.name}</center>
            </span>
        </div>
    );
}
