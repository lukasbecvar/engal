import React from 'react';
import { BrowserRouter as Router, Routes, Route} from 'react-router-dom';

// import components
import SetupComponent from './component/SetupComponent';
import DashboardComponent from './component/DashboardComponent';
import NotFoundComponent from './component/sub-component/NotFoundComponent';

// import engal utils
import { getApiUrl } from './util/StorageUtil';

// import app style
import './assets/css/main.css';

export default function App() {
    // get api url from local storage
    let api_url = getApiUrl();
    
    // check if api url seted
    if (api_url == null) {
        // render api setup component
        return <SetupComponent/>
    }

    // render component by route
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<DashboardComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    );
}
