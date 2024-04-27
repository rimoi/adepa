$(function () {
    var $sport = $('#category_type');
    $sport.on('change', function() {
        $('.js-loader').addClass('spinner-border')

        var $form = $(this).closest('form');

        var data = {};
        data[$sport.attr('name')] = $sport.val();

        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            success: function(html) {

                $('#category_parent').replaceWith(
                    $(html).find('#district_country')
                );

                $('#category_parent').css('width', '100%');

                $('#category_parent').select2();

                setTimeout(function () {
                    $('.js-loader').removeClass('spinner-border')
                }, 500);
            }
        });
    });
});