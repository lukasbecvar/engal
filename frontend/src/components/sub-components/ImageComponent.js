export default function ImageComponent(props) {
    return (
        <a href={'data:image/jpg;base64,' + props.image} data-sub-html={props.name}>
            <img className="gallery-images" alt={props.description} src={'data:image/jpg;base64,' + props.image}/>
        </a>
    );
}
