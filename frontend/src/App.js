import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate} from 'react-router-dom';

// import components
import Home from './component/Home';
import Login from './component/Auth/Login';
import NotFound from './component/sub-component/NotFound';

export default function App() {
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<Home/>}/>
                <Route path="/login" element={<Login/>}/>
                <Route path="*" element={<NotFound/>}/>
            </Routes>
        </Router>
    );
}
