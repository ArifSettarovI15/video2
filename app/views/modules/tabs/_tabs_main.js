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
}