export default function ErrorMessageComponent(props) {
    let message = props.message == null ? 'Unknown error' : props.message

    return <h1>{message}</h1>
}
