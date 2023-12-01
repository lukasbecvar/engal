// init react components
import React from 'react';
import ReactDOM from 'react-dom/client';

// import main app style
import './assets/css/main.css';

// init main app component
import Main from './components/Main';

// create app root
const root = ReactDOM.createRoot(document.getElementById('root'));

// render main component
root.render(<Main/>);
