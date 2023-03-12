import "@lottiefiles/lottie-player";

import data_plateform from './animations/plateform.json';
import data_formation from './animations/formation.json';
import data_assistance from './animations/assistance.json';
import $ from "jquery";

// const player = document.querySelector("lottie-player");

// player.addEventListener("rendered", (e) => {
//     //Load via URL
//     player.load(data);
//
// });

$(document).ready(function() {
    const plateform = document.querySelector(".js-plateforme");
        //Load via URL
    plateform.load(data_plateform);

    const formation = document.querySelector(".js-formation");
    //Load via URL
    formation.load(data_formation);

    const assistance = document.querySelector(".js-assistance");
    //Load via URL
    assistance.load(data_assistance);
});



