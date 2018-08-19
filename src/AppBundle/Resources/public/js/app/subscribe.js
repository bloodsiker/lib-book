jQuery(function() {
    var popup = jQuery('.subscribe_wrapper');
    var overlay = jQuery('.popup_overlay');
    var closePopupButton = jQuery('.close_popup__btn');

    var showPopup = function () {
        popup.addClass('active');
    };
    var hidePopup = function () {
        popup.removeClass('active');
        jQuery.ajax({
            method: 'GET',
            url: '/esputnik/cookie_on_close'
        });
    };

    closePopupButton.on('click', hidePopup);
    overlay.on('click', hidePopup);

    jQuery('.subscribe__form form').submit(function (e) {
        var form = jQuery(this);
        form.find('[name="email"]').removeClass('input_error');
        form.find('.error-text').hide();
        e.preventDefault();
        jQuery.ajax({
            method: 'POST',
            url: '/esputnik/subscribe',
            dataType: 'json',
            data: { email: form.find('[name="email"]').val() },
            success: function (data) {
                if (data.status === 0) {
                    form.find('[name="email"]').addClass('input_error');
                    form.find('.error-text').show();
                } else if (data.status === 1) {
                    form.find('[name="email"]').val('');
                    jQuery('.subscribe_content').hide();
                    jQuery('.subscribe_success').show();
                }
            }
        });
    });
});