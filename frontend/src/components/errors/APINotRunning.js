// import react
import { useState, useEffect } from "react";

// import app components
import LoadingComponent from "../LoadingComponent.js";

const APINotRunning = () => {

    // default show state
    const [show, setShow] = useState(false);

    // set true for set msg after 10 sec.
    useEffect(() => {
        window.setTimeout(() => {
            setShow(true)
        }, 10000);
    });

    // return msg
    if (show) {
        return <h1>API connection error or api have maitenance please try ag ltr(server error)</h1>
    } else {
        return <LoadingComponent></LoadingComponent>
    }
};
  
export default APINotRunning;