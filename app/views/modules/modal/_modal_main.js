var js_popup=$(".js-popup");
if (js_popup.length>0) {
    js_popup.magnificPopup({
        type: 'ajax',
        fixedContentPos: true,
        fixedBgPos: true,
        closeMarkup:'<div class="mfp-close"></div>',
        overflowY: 'auto',
        preloader: false,
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in',
        callbacks: {
            beforeOpen: function() {
                StartAjaxLoading();
                if (top_slider!==false) {
                    top_slider.slick('slickPause');
                }
            },
            ajaxContentAdded: function() {
                StopAjaxLoading();
                if ($('.js-auto-size').length>0) {
                    $('.js-auto-size').textareaAutoSize();
                }

            },
          open: function() {
            onOpenModal();
          },
            close: function() {
                if (top_slider!==false) {
                    top_slider.slick('slickPlay');
                }
              CloseModals();
            }
        }
    });
};

$(document).on('click', '.popup-open', function(e) {
    e.preventDefault()
    openInlineModal($(this).attr('data-mfp-src'), $(this))
})

$(document).on('click', '.js-modal-close', function() {
    CloseModals()
})
