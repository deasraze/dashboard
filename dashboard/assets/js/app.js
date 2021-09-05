/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.scss';

import 'bootstrap';
import '@coreui/coreui';

const Centrifuge = require('centrifuge');

document.addEventListener('DOMContentLoader', function () {
    const centrifuge = new Centrifuge('ws://localhost:8083/connection/websocket');

    centrifuge.subscribe('alerts', function (message) {
        console.log(message.data.message);
    });

    centrifuge.connect();
});
