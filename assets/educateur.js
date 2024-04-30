/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/nav.css';
import './styles/home.css';
import './styles/footer.css';
import './styles/section_atelier.css';



import './admin/vendor/fontawesome-free/css/all.min.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'flatpickr/dist/themes/material_blue.css';
import './styles/educateur.css';
import './styles/article.css';

import 'select2/dist/css/select2.min.css';


import $ from 'jquery';
window.$ = global.$ = $;

require('bootstrap');

import {Modal} from "bootstrap";

import * as Turbo from '@hotwired/turbo';

// Disabled Turbo
Turbo.session.drive = 0;

import 'select2';
import 'select2/dist/js/i18n/fr';

import flatpickr from 'flatpickr';
import French from 'flatpickr/dist/l10n/fr';

import Swal from 'sweetalert2/dist/sweetalert2.all';


window.Swal = global.Swal = Swal;


const myModal = new Modal('#staticBackdrop', {
    keyboard: false
})

import './scripts/fiche-educateur';
import './scripts/my.js';

