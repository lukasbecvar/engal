export default function ImageComponent(props) {
    return (
        <a href={props.image} data-sub-html={props.name}>
            <img className="gallery-images" alt={props.name} src={props.image}/>
        </a>
    );
}
