/**
 * Renders the main application component into the root element using ReactDOM.createRoot.
 * @module index
 * @requires React
 * @requires ReactDOM
 * @requires App
 */
import React from 'react';
import ReactDOM from 'react-dom/client';

// import main app component
import App from './App';

// render app root
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
    <React.StrictMode>
        <App/>
    </React.StrictMode>
);
