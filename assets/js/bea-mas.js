var fr;
if (!fr) {
    fr = {};
} else if (typeof fr != "object") {
    throw new Error('fr already exists and not an object');
}

if (!fr.bea_mas) {
    fr.bea_mas = {};
} else if (typeof fr.bea_mas != "object") {
    throw new Error('fr.bea_mas already exists and not an object');
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

(function ($) {

    $(bea_mas.jquery_target).waypoint({
        continuous: false,
        handler: function () {
            fr.bea_mas.counter.init();
            this.destroy()
        }
    })

    $('.tooltip').tooltipster({
        contentCloning: false,
        theme: 'tooltipster-shadow'
    });

})(window.jQuery);