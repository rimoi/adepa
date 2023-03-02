/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './admin/scss/sb-admin-2.scss';
import './admin/vendor/fontawesome-free/css/all.min.css';
import './styles/bo.css';
import 'flatpickr/dist/themes/material_blue.css';
import './styles/carousel.css';


import $ from 'jquery';
import "./admin/vendor/bootstrap/js/bootstrap.bundle.min.js";
import "./admin/vendor/jquery-easing/jquery.easing.min.js";
import "./admin/js/sb-admin-2.min.js";

import Routing from 'fos-router';

window.Routing = Routing;

import flatpickr from 'flatpickr';
import French from 'flatpickr/dist/l10n/fr';

import Swal from 'sweetalert2/dist/sweetalert2.all';

window.$ = global.$ = $;
window.Swal = global.Swal = Swal;


import './scripts/my';