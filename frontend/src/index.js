// react & libs
import ReactDOM from "react-dom/client";
import { useState } from "react";

// application config
import {API_URL, API_TOKEN} from "./config.js"

// app components
import MainComponent from "./components/MainComponent.js";

// app error components
import APINotRunning from "./components/errors/APINotRunning.js";

// import global css style
import "./assets/css/main.css"

// export default app build
export default function App() {
    
    // init use state for api values
    const [status, setStatus] = useState(null)

    // get api status and update state
    const setAPIStatus = async () => {

        // fetch json from urls
        const response = await fetch(API_URL + "?token=" + API_TOKEN + "&action=status")

        // get object from url
        const data = await response.json() 

        // set status with use state
        setStatus(data["status"])
    }

    // call api value set
    setAPIStatus()
    ///////////////////////////////////////////////////////////////////////////


    // check if api running 
    if (status != "running") {
        return <APINotRunning/>
    } else {

        // return valid main component
        return <MainComponent/>
    }
}

// render default app index
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);