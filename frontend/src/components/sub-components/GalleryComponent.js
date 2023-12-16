export default function GalleryComponent(props) {
    return (
        <div className="gallery-link" id="gallery2">
            <a href="#" className="link-text">
                <img className="image-link" src={'data:image/jpg;base64,' + props.thumbnail} alt={props.name}/>
                {props.name}
            </a>
        </div>
    );
}
