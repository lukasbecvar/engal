export default function ImageComponent(props) {
    return (
        <div id="lightgallery">
            <a href={'data:image/jpeg;base64,' + props.image} data-sub-html={props.name}>
                <img className="gallery-images" alt={props.name} src={'data:image/jpeg;base64,' + props.image}/>
            </a>
        </div>
    );
}
