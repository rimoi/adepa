import French from "flatpickr/dist/l10n/fr";
import $ from "jquery";

$(function () {
    const datePicker = flatpickr(".js-datepicker", {
        enableTime: true,
        dateFormat: 'd/m/Y',
        defaultHour: '09',
        minDate: "today", // Définir la date minimale sur aujourd'hui
        // maxDate: new Date().fp_incr(1), // Définir la date maximale sur demain
        locale: French.fr,
        onChange: function(selectedDates, dateStr, instance) {
            instance.close(); // Fermer le sélecteur de date Flatpickr
        }
    });

    flatpickr(".datepicker", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        defaultHour: '09',
        minDate: "today", // Définir la date minimale sur aujourd'hui
        // maxDate: new Date().fp_incr(1), // Définir la date maximale sur demain
        locale: French.fr,
        onChange: function(selectedDates, dateStr, instance) {
            instance.close(); // Fermer le sélecteur de date Flatpickr
        }
    });

    if ($('.js-select2').length) {
        let input_select2 = $('.js-select2');
        input_select2.select2({
            allowClear: true,
            placeholder: input_select2.attr('placeholder'),
            dropdownParent: $("#staticBackdrop")
        });
    }

    $('.js-intervention').on('input', function() {
        var intervention = $(this).val();
        var priceEducatheur = $(this).data('price');
        var price = intervention * priceEducatheur;
        $('.js-price').val(price + ' €');
    });

    const menuHamburger = document.querySelector(".menu-burger");
    const navLinks = document.querySelector(".nav-links");

    if (menuHamburger) {
        menuHamburger.addEventListener('click', () => {
            navLinks.classList.toggle('mobile-menu')
        });
    }
});