import React from 'react';
import ReactDOM from 'react-dom/client';

// import main app style
import './assets/css/main.css';

// init main app component
import MainComponent from './components/MainComponent';

// create app root
const root = ReactDOM.createRoot(document.getElementById('root'));

// render main component
root.render(<MainComponent/>);
