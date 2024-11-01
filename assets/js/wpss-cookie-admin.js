/**
 * Fixed admin social links bar
 */
jQuery(function ($) {
    $(window).on('scroll', function () {
        let wpss_container = $('.wpss-container');
        let wpss_container_left = wpss_container.offset().left;
        let wpss_container_top = wpss_container.offset().top;
        let wpss_container_width = wpss_container.outerWidth();
        let social_position_left = 40 + wpss_container_left + wpss_container_width + "px";
        let social_position_top = wpss_container_top + "px";
        if ($(this).scrollTop() > wpss_container_top) {
            $('.wpss-social-links').addClass('wpss-social-fixed')
                .css('left', social_position_left)
                .css('top', social_position_top);
        } else {
            $('.wpss-social-links').removeClass('wpss-social-fixed').removeAttr('style');
        }
    });
});