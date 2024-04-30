import $ from "jquery";

$(function () {
    if ($('.js-cancel-booking').length) {
        $('.js-cancel-booking').on('click', function (e) {
            e.preventDefault();

            var url = $(this).data('url');

            Swal.fire({
                title: 'Etes-vous sûr de vouloir annuler la réservation ?',
                showCancelButton: true,
                confirmButtonText: 'Confirmer',
                cancelButtonText: `Fermer`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    }

    if ($('.js-select2').length) {
        let input_select2 = $('.js-select2');
        input_select2.select2({
            allowClear: true,
            placeholder: input_select2.attr('placeholder'),
            dropdownParent: $("#staticBackdrop")
        });
    }
});