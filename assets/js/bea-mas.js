var fr;
if (!fr) {
    fr = {};
} else if (typeof fr != "object") {
    throw new Error('fr already exists and not an object');
}

if (!fr.bea_mas) {
    fr.bea_mas = {};
} else if (typeof fr.bea_mas != "object") {
    throw new Error('fr.cauet already exists and not an object');
}

fr.bea_mas.counter = {
    ajaxing: false,
    init: function () {
        jQuery.ajax({
            url: bea_mas.ajax_url,
            dataType: 'json',
            method: 'post',
            data: {
                _wpnonce: bea_mas.ajax_nonce,
                id: bea_mas.current_object_id
            },
            beforeSend: function () {
                fr.bea_mas.counter.ajaxing = true;
            },
            success: function (response) {
                fr.bea_mas.counter.ajaxing = false;
            }
        })

    }
};

jQuery(document).ready(function () {
    var flag = false;
    jQuery(window).scroll(function () {
        var offset = jQuery(window).height();
        var height = jQuery('#main').outerHeight(true);
        var scroll = jQuery(window).scrollTop();
        if (scroll >= (height - offset) && !flag) {
            setTimeout(function () {
                fr.bea_mas.counter.init();
            }, 3000);
            flag = true;
        }
    });
});

/**
(function($){

    $('.vu__filter input[type="radio"]').on('mouseenter', function () {
        $('.vu__filter input[type="radio"]').prop('checked', false);
        $(this).prop('checked', true);
        console.log('hover');
    });

    $('.vu__filter input[type="radio"]').on('click', function (e) {
        e.preventDefault();
        return false;
    });

    $(document).ready(function() {
        // $('#menu1').dropit();
        $('#menu1').dropit({
            action: 'hover',
            submenuEl: 'div'
        });
    });


})(window.jQuery);**/