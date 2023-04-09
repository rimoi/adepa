import $ from "jquery";
import French from "flatpickr/dist/l10n/fr";


// ADMIN

// button suppression
$(document).ready(function() {

    const menuHamburger = document.querySelector(".menu-burger");
    const navLinks = document.querySelector(".nav-links");

    if (menuHamburger) {
        menuHamburger.addEventListener('click', () => {
            navLinks.classList.toggle('mobile-menu')
        });
    }

    if ($('.js-select2').length) {
        let input_select2 = $('.js-select2');
        input_select2.select2({
            allowClear: true,
            placeholder: input_select2.attr('placeholder'),
        });
    }

    $('.js-deleted').on('click', function (e) {
        e.preventDefault();

        var url = $(this).data('url');

        Swal.fire({
            title: 'Voulez-vous bien confirmer la suppression',
            showCancelButton: true,
            confirmButtonText: 'Valider',
            cancelButtonText: `Annuler`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })

    });

    $('.js-show-element').on('click', function (e) {
        e.preventDefault();

        let element_class = $(this).data('classParent');
        $(this).closest('.js-macro-upload-image').find(element_class).toggleClass('d-none');

        $(this).closest('.js-parent-element').addClass('d-none');
    });


    $('.add-another-collection-widget').click(function (e) {
        var list = $($(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);

        addTagFormDeleteLink(newElem);
    });

    document
        .querySelectorAll('#experience-fields-list .js-genus-scientist-item')
        .forEach((tag) => {
            addTagFormDeleteLink(tag)
        })


    function addTagFormDeleteLink(item) {

        const div = document.createElement('div');
        div.className = 'col-12 col-sm-2 text-center text-sm-left';

        const removeFormButton = document.createElement('button');
        removeFormButton.innerText = 'Supprimer';
        removeFormButton.className = 'btn-sm btn-danger mt-2';
        removeFormButton.title= 'Supprimer cette experience'

        div.append(removeFormButton);

        item.append(div);

        removeFormButton.addEventListener('click', (e) => {
            e.preventDefault();
            // remove the li for the tag form
            item.remove();
        });
    }

    // Qualification
    document
        .querySelectorAll('#qualification-fields-list .js-genus-scientist-item')
        .forEach((tag) => {
            addTagFormDeleteLink(tag)
        });

    flatpickr(".datepicker", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        defaultHour: '09',
        locale: French.fr
    });

    // Front
    $('.js-tooltip').tooltip();

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

     $('.js-booking-mission').on('click', function (e) {
        e.preventDefault();

        var url = $(this).data('url');

        Swal.fire({
            title: "Vous acceptez les conditions du contrat en cliquant sur le bouton 'Confirmer'",
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

    $('.js-validate-url').on('click', function (e) {
        e.preventDefault();

        var slug = $(this).data('slug');
        var element = $(this).data('element');

        Swal.fire({
            title: 'Etes-vous sûr de vouloir activer cet utilisateur ?',
            showCancelButton: true,
            confirmButtonText: 'Confirmer',
            cancelButtonText: `Fermer`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: window.Routing.generate('admin_user_activate', {slug}),
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'Utilisateur activé avec succés',
                                showConfirmButton: false,
                                timer: 1500
                            })

                            $('.'+element).fadeOut('slow');
                        }
                    }
                });
            }
        })
    });

    if ($('.js-event-change-service').length) {


       var $service = $('.js-event-change-service');

        $service.on('change', function () {

            $('.js-toggle-spiner').toggleClass('d-none');

            var data = {};
            $.ajax({
                url : window.Routing.generate('admin_mission_get_info_service', {'id':  $service.val()}),
                type: 'GET',
                complete: function(data) {

                    let service = data.responseJSON;

                    $('#mission_address').val(
                        service.address
                    );
                    $('#mission_zipCode').val(
                        service.zipCode
                    );
                    $('#mission_city').val(
                        service.city
                    );
                    $('#mission_phone').val(
                        service.phone
                    );

                    $('.js-toggle-spiner').toggleClass('d-none');
                }
            });
        });
    }

    if ($('.js-show-cookie').length) {
        $('.js-save-cookie-modal').on('click', function (e) {
            e.preventDefault();

            setCookie("session-cookie-condition", "123456789", 7);

            $('.js-close-modal').trigger('click');

            Swal.fire({
                icon: 'success',
                title: 'Cookie sauvegardé',
                showConfirmButton: false,
                timer: 1500
            });

            $('.js-show-cookie').fadeOut('slow');
        });

        $('.js-save-cookie').on('click', function (e) {
            e.preventDefault();

            setCookie("session-cookie-condition", "123456789", 7);

            Swal.fire({
                icon: 'success',
                title: 'Cookie sauvegardé',
                showConfirmButton: false,
                timer: 1500
            });

            $('.js-show-cookie').fadeOut('slow');
        });
    }

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
});

