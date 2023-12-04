function MainComponent() {
    return (<p>logged: {localStorage.getItem('user-token')}</p>)
}

export default MainComponent;
