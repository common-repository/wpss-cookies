/**
 * Hide cookie message container
 */
jQuery(function ($) {
    $('body').on('click', '.wpss-cookie-button-accept', function (e) {
        e.preventDefault();
        let wpss_cookie_message = $('.wpss-cookie-message');
        $.ajax({
            url: wpss_cookie_request.ajaxurl,
            type: 'POST',
            cache: false,
            data: {
                action: 'wpss_set_cookie_action',
                security: wpss_cookie_request.securitynonce,
            },
            beforeSend: function () {
                $('.wpss-cookie-button-accept').addClass('wpss-loading-btn');
            },
        }).success(function () {
            wpss_cookie_message.addClass('wpss-hide-message');
        });
    });
});