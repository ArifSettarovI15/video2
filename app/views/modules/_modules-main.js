$(function() {
$( document ).on( "focus click", ".element.input-style_error", function() {
    ClearElement($(this));
});
$( document ).on( "click", ".form .submit:not(.disabled)", function() {
    var form_obj=$(this).closest('.form');
    AddObject(form_obj);
});

$('.form input.element[type="text"],.form input.element[type="password"],.form input.element[type="email"]').keyup(function(e){
    if(e.keyCode == 13)
    {
        var form_obj=$(this).closest('.form');
        AddObject(form_obj);
    }
});
$('form.form').submit( function(e){
    e.preventDefault();
});

$( document ).on( "click focus", ".input__input", function(e) {
    if ($(this).hasMod('disabled')) {

    }
    else {
        // cleanActiveInputs();
        $(this).block().mod('active', true);
    }
});

initForms();

$(document).on('input', '.field__input', function(e) {
    var $close = $(this).siblings('.field__clean')

    if ($(this).val().length > 0) {
        $(this).addClass('focused');
        if ($(this).val().length > 2) {
            $close.show()
        }
    } else {
        $close.hide()
        $(this).removeClass('focused');
    }
})

$(document).on('click tap', '.js-input-clean', function(e) {
    var input = $(this).siblings('input')
    input.val('').removeClass('focused')
    $(this).hide()
    $(this).block().mod('opened', false)
})

$('.js-acc-head').each(function(i, elem) {
    var hammerTime = new Hammer(elem, {
        domEvents: true
    })

    hammerTime.on('tap', function() {
        slideFooterMenu($(elem))
    })
})

function slideFooterMenu(trigger) {
    var parent = trigger.closest('.js-acc-parent')
    parent.siblings().mod('active', false);
    if (parent.hasMod('active')) {
        parent.mod('active', false);
    } else {
        parent.mod('active', true);
    }
}

initScroll($('.js-scroll'))

$(document).on('keyup', '.field_search .field__input', function (e) {
    if ($(this).val() !== '') {
        $(this).block().mod('opened', true);
    } else {
        $(this).block().mod('opened', false)
    }
    initScroll($(this).block().find('.field-list'))
    // searchTop($(this).block());

});

$(document).on('click', '.js-search-select', function() {
    var val = $(this).html().replace(/<\/?[a-zA-Z]+>/gi,'').trim();
    $(this).closest('.field').elem('input').attr('data-id',$(this).attr('data-id')).val(val);
    closeSeachDop();
})

$(document).on('click', function(e) {
    var a = $('.field_search.field_opened');

    a.is(e.target) || a.has(e.target).length !== 0 || closeSeachDop();
})

$('.textfield__field').textareaAutoSize()

function closeSeachDop() {
    $('.field_search').mod('opened', false);
    $('.js-search-block__form-list').mod('hide', true);
}



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

$( document ).on( "click", "#notification .close", function() {
    HideNotification();
});


$(document).on('click', '.tabs__link', function () {
    if ($(this).hasClass('no-js')) {
        return
    } else if ($(this).hasClass('ajax-tab')) {
        var data_array = {}
        data_array['action'] = 'get_filter'
        data_array['id'] = $(this).attr('data-id')
        var form_obj = $(this).closest('.tabs')
        var options = {}
        options['AfterDone'] = tabContentDone
        options['list'] = form_obj.attr('data-list')
        options['parent'] = form_obj
        options['current'] = $(this)
        SendAjaxRequest(
            {
                'url': form_obj.attr('data-url'),
                'data': data_array,
                'options': options,
            }
        );
    } else {
        TabsSet($(this))

        if ($(this).attr('data-val')) {
            var block = $(this).closest('.tabs').attr('data-tab')
            chainSelectWithTab($('.tabs[data-chain="' + block + '"').find('[data-val="' + $(this).attr('data-val') + '"]'))
        }
    }
})


$(document).on('click', '.js-trigger-select', function () {
    chainSelectWithTab($(this))
    var chainTabs = $('.tabs[data-tab="' + $(this).closest('.tabs').attr('data-chain') + '"]');
    TabsSet(chainTabs.find('[data-val="' + $(this).attr('data-val') + '"]'))
})

function chainSelectWithTab(item) {
    var val = item.attr('data-val')
    var select = item.closest('.form').find('select[name="' + item.attr('data-select') + '"]')
    select.val(val).trigger('change')
    $('.js-trigger-select').mod('active', false)
    item.mod('active', true)
    if (isInputHide()) {
        inputShow()
    } else {
        if (item.attr('data-val') === 'driver') {
            inputHide()
        }
    }
}

function inputHide() {
    var input = $('.input-toggle')

    input.addClass('hidden')
    input.find('input').removeClass('element required')
}

function isInputHide() {
    var input = $('.input-toggle')
    return input.hasClass('hidden')
}

function inputShow() {
    var input = $('.input-toggle')
    input.removeClass('hidden')
    input.find('input').addClass('element required')
}

function tabContentDone(response, ajax_config, textStatus, jqXHR) {

    if (response.status) {
        var table = $('.table-content[data-list="' + ajax_config.options.list + '"]')
        table.html(response.html)
        table.next().html(response.paging)
        ajax_config.options.parent.find('.tabs__link').mod('active', false).attr('data-active',0);
        ajax_config.options.current.mod('active', true).attr('data-active',1)
    }
}});