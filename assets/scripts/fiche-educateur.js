import French from "flatpickr/dist/l10n/fr";

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
});