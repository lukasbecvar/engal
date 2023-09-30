/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (bases/base.html.twig).
 */

// bootstrap
import './bootstrap.scss';

// custom css
import './css/main.css';
import './css/scrollbar.css';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
