import React from 'react';
import ReactDOM from 'react-dom/client';

// import bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// import main app style
import './assets/css/main.css';
import './assets/css/scrollbar.css';

// init main app component
import InitComponent from './InitComponent';

// create app root
const root = ReactDOM.createRoot(document.getElementById('root'));

// render main component
root.render(<InitComponent/>);
