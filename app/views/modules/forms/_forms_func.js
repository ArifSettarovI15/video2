function CheckForm(obj) {
    var error = 0;
    obj.find(".element").each(function () {
        var elem = $(this);
        if (elem.hasClass('required')) {
            if (elem.attr('type') == 'checkbox' || elem.attr('type') == 'radio') {
                if (elem.closest('.form').find('[name="' + elem.attr('name') + '"].element:checked').length > 0) {
                    SetOkToElement(elem.closest('.radio-label'));
                } else {
                    error = 1;
                    SetErrorToElement(elem.closest('.radio-label'));
                }
            } else if (elem.hasClass('select_2')) {
                SetOkToElement(elem.next('.select_2'));
            } else {
                if ((elem.val() == '' || elem.val() == null) && !elem.hasClass('.select2-hidden-accessible')) {
                    error = 1;
                    SetErrorToElement(elem);
                } else {
                    SetOkToElement(elem);
                }
            }
        }
    });


    if (error == 0) {
        return true;
    } else {
        ShowError('Заполните необходимые поля');
        return false;
    }
}

function SendForm(form_obj, form_options) {
    form_options = form_options || {};

    var options = {
        'form_obj': form_obj
    };
    $.each(form_options, function (index, value) {
        options[index] = value;
    });

    if (options.BeforeFunc) {
        options.BeforeFunc(form_obj, options);
    }
    var data = GetFormData(form_obj);

    if (CheckForm(form_obj)) {
        SendAjaxRequest(
            {
                'data': data,
                'options': options,
                'onComplete': FormComplete,
                'onBefore': FormBefore,
                'ShowLoading2': true
            }
        );
    }
}

function ClearElement(elem) {
    elem.removeClass('input-style_ok');
    elem.removeClass('input-style_error');
    if (elem.attr('type') != 'checkbox') {
        elem.parent().removeClass('element_value_ok');
        elem.parent().removeClass('element_value_error');
    }
}

function SetOkToElement(elem) {
    elem.addClass('input-style_ok');
    elem.removeClass('input-style_error');
    if (elem.hasClass('select2-hidden-accessible')) {
        SetOkToElement(elem.next('.select2-container'));
    }
}

function SetErrorToElement(elem) {
    if (elem.length) {
        elem.addClass('input-style_error');
        elem.removeClass('input-style_ok');
        if (elem.hasClass('select2-hidden-accessible')) {
            SetErrorToElement(elem.next('.select2-container'));
        }
    }
}

function EnableElement(target) {
    if (target.is("input") || target.is("select")) {
        target.prop('disabled', false);
        target.val(0);
    }
    target.removeClass('disabled');

}

function DisableElement(target) {
    if (target.is("input") || target.is("select")) {
        target.prop('disabled', true);
        if (target.is("select")) {
            target.html('<option value="0">-</option>');
        }
    }
    target.addClass('disabled');

}

function FormComplete(response, ajax_config, textStatus, jqXHR) {
    // EnableElement(ajax_config.options.form_obj.find('.submit'));
}

function FormBefore(ajax_config, jqXHR) {
    ClearElement(ajax_config.options.form_obj.find('.element'))
    // DisableElement(ajax_config.options.form_obj.find('.submit'));
}

function SetMultilineData(id) {
    var values = [];
    $($('.multiline_container[data-value="' + id + '"] .multiline_data >div')).each(function (index, value) {
        var item_value = {};
        $($(this).find('.sub_element')).each(function (index, value) {
            item_value[$(this).attr('data-name')] = $(this).val();
        });
        values.push(item_value);
    });
    $('.multiline_container[data-value="' + id + '"] [data-value="complete_input"]').val(JSON.stringify(values));
}

function AddObject(form_obj) {
    if (form_obj.attr('data-before')) {
        window[form_obj.attr('data-before')];
    }
    var data = GetFormData(form_obj);
    var options = {};
    options['form_obj'] = form_obj;
    if (CheckForm(form_obj)) {
        SendAjaxRequest(
            {
                'url': form_obj.attr('data-url'),
                'data': data,
                'options': options,
                'onComplete': FormComplete,
                'onBefore': FormBefore
            }
        );
    }
}

function GetFormData(form_obj) {
    var data = {};
    if (typeof (tinyMCE) != "undefined") {
        tinyMCE.triggerSave(true, true);
    }
    form_obj.find(".element").each(function () {
        var elem = $(this);
        if (elem.closest('.form').is(form_obj)) {
            var type = elem.attr('type');
            if ((elem.attr('data-no-empty') && elem.val() != 0 && elem.val() != '') ||
                !elem.attr('data-no-empty')) {
                if (elem.attr('data-value') == 'complete_input') {
                    SetMultilineData(elem.closest('.multiline_container').attr('data-value'));
                } else if (elem.hasClass('smart-input')) {
                    if (elem.attr('data-value')) {
                        data[elem.attr('name')] = elem.attr('data-value');
                    } else {
                        data[elem.attr('name')] = elem.attr('data-id');
                    }

                } else if (elem.attr('data-type') == 'switch') {
                    data[elem.attr('data-name')] = elem.find('.active').attr('data-value');
                } else if (type == 'checkbox') {
                    var val = false;
                    if (elem.prop('checked')) {
                        if (elem.hasClass('element_array')) {
                            if (data[elem.attr('name')]) {

                            } else {
                                data[elem.attr('name')] = [];
                            }
                            data[elem.attr('name')].push(elem.val());

                        } else {
                            val = true;
                            if (elem.attr('value')) {
                                val = elem.attr('value');
                            }
                            if (elem.attr('name')) {
                                data[elem.attr('name')] = val;
                            }
                        }
                    }
                } else if (type == 'radio') {
                    if (elem.is(':checked')) {
                        data[elem.attr('name')] = elem.val();
                    }
                } else {
                    if (elem.attr('name')) {
                        if (elem.hasClass('element_array')) {
                            if (data[elem.attr('name')]) {

                            } else {
                                data[elem.attr('name')] = [];
                            }
                            data[elem.attr('name')].push(elem.val());
                        } else if (elem.hasClass('select_multiple')) {
                            var str = "";
                            elem.siblings('input').each(function (i, item) {
                                if ($(item).val()) {
                                    str += $(item).attr('data-name') + ":" + $(item).val() + ", ";
                                }
                            });
                            data[elem.attr('name')] = str
                        } else if (elem.prop('multiple')) {
                            if (elem.val()) {
                                data[elem.attr('name')] = elem.val().join('; ');
                            } else {
                                data[elem.attr('name')] = elem.val();
                            }
                        } else {
                            data[elem.attr('name')] = elem.val();
                        }


                    }
                }
            }
        }

    });
    return data;
}

function LogoutUser(obj) {
    var options = {};
    options['form_obj'] = obj;
    options['reload'] = false;
    options['AfterDone'] = LogoutUserDone;
    SendAjaxRequest(
        {
            'url': obj.attr('data-url'),
            'options': options,
            'ShowLoading2': true
        }
    );
}

function LogoutUserDone(response, ajax_config, textStatus, jqXHR) {
    window.location.href = home_url;
}

var maskList = false;

function MaskPhone() {
  if (
    $('input[name="phone"]').length>0 && $('input[name="phone"]').is(':visible')

  ) {
    $('input[name="phone"]').inputmask('+9(999) 999-9999',{
      clearMaskOnLostFocus: true,
      showMaskOnHover: false,
      showMaskOnFocus: true,
      autoUnmask: true,
      positionCaretOnClick: 'lvp'
    });
  }
}



function initForms() {
    var $selects = $('.select_2').not('.select2-hidden-accessible');
    $selects.each(function (i, elem) {
        $(elem).select2({
            width: '100%',
            placeholder: ' ',
            theme: $(elem).attr('data-theme'),
            dropdownParent: $(elem).closest('.select'),
            minimumResultsForSearch: $(elem).attr('data-search') || Infinity,
            closeOnSelect: !$(elem).prop('multiple'),
            language: {
                noResults: function (params) {
                    return 'Результаты не найдены';
                }
            }
        })

        if ($(elem).val()) {
            $(elem).select2('focus')
            changeSelectedClass($(elem))
        }

        $(elem).on('select2:open', function (e) {
            var evt = 'scroll.select2';
            $(e.target).parents().off(evt);
            $(window).off(evt);
            setTimeout(function () {
                initScroll($('.select2-results__options'))
            }, 0)
        })

        $(elem).on('select2:select', function (e) {
            if ($(e.currentTarget).attr('name') === 'from' || $(e.currentTarget).attr('name') === 'to') {
                var data = e.params.data;
                $(e.currentTarget).attr('data-coord', $(data.element).attr('data-coord'))
                initMapRoute()
                hideSimilarValues(e)
            }

            if ($(e.currentTarget).attr('data-type') === 'score') {
                filterFeeds(e)
            } else if ($(e.currentTarget).attr('data-type') === 'status') {
                filterOrders(e)
            }
        })

        $(elem).on('select2:select', function (e) {
            changeSelectedClass(e.target)
        })
    })


    MaskPhone();
    initCbsAndRadio();

}


function changeSelectedClass(elem) {
    if ($(elem).val() !== '') {
        $(elem).next().addClass('selected')
    } else {
        $(elem).next().removeClass('selected')
    }
}

function hideSimilarValues(e) {
    var otherSelect = $('select').not(e.currentTarget)
    otherSelect.find('[data-value]').prop('disabled', false)
    otherSelect.find('[data-value="' + $(e.params.data.element).attr('data-value') + '"]').prop('disabled', true)
}

// function formatState(state) {
//     return state.text
// }

function formatMultipleResult(state) {
    return $(
        '<span>' + state.text + '<span class="checkbox"><span class="checkbox__icon"><svg viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path fill-rule="evenodd" clip-rule="evenodd" d="M14.0405 3.2968C14.431 3.68732 14.431 4.32049 14.0405 4.71101L6.70719 12.0443C6.31666 12.4349 5.6835 12.4349 5.29297 12.0443L1.95964 8.71101C1.56912 8.32049 1.56912 7.68732 1.95964 7.2968C2.35017 6.90627 2.98333 6.90627 3.37385 7.2968L6.00008 9.92303L12.6263 3.2968C13.0168 2.90628 13.65 2.90628 14.0405 3.2968Z" fill="#86DBF1"/>\n' +
        '</svg>\n</span></span></span>'
    )
}

function initCbsAndRadio() {
    var templates = {
        checkbox: '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path fill-rule="evenodd" clip-rule="evenodd" d="M14.0405 3.29289C14.431 3.68342 14.431 4.31658 14.0405 4.70711L6.70713 12.0404C6.3166 12.431 5.68344 12.431 5.29291 12.0404L1.95958 8.70711C1.56906 8.31658 1.56906 7.68342 1.95958 7.29289C2.3501 6.90237 2.98327 6.90237 3.37379 7.29289L6.00002 9.91912L12.6262 3.29289C13.0168 2.90237 13.6499 2.90237 14.0405 3.29289Z" fill="white"/>\n' +
            '</svg>',
        radio: '<svg viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path fill-rule="evenodd" clip-rule="evenodd" d="M8.88386 2.8422C10.4264 2.4937 12.0402 2.65314 13.4847 3.29676C13.9891 3.52154 14.5803 3.29481 14.8051 2.79033C15.0299 2.28586 14.8031 1.69468 14.2987 1.46991C12.4602 0.650752 10.4063 0.44782 8.4431 0.891374C6.47992 1.33493 4.71272 2.4012 3.40505 3.93117C2.09738 5.46114 1.31931 7.37283 1.18689 9.38113C1.05446 11.3894 1.57477 13.3867 2.67022 15.0752C3.76567 16.7636 5.37756 18.0527 7.2655 18.7502C9.15343 19.4477 11.2163 19.5162 13.1463 18.9455C15.0764 18.3749 16.7703 17.1956 17.9754 15.5836C19.1805 13.9716 19.8322 12.0132 19.8333 10.0006V10V9.23333C19.8333 8.68105 19.3856 8.23333 18.8333 8.23333C18.281 8.23333 17.8333 8.68105 17.8333 9.23333V9.99943C17.8324 11.5808 17.3204 13.1195 16.3735 14.3861C15.4266 15.6527 14.0957 16.5792 12.5793 17.0276C11.0628 17.476 9.44198 17.4222 7.9586 16.8741C6.47522 16.3261 5.20874 15.3132 4.34803 13.9866C3.48732 12.66 3.0785 11.0907 3.18255 9.51273C3.2866 7.93478 3.89794 6.43273 4.9254 5.23061C5.95285 4.0285 7.34137 3.19071 8.88386 2.8422ZM19.5408 4.04008C19.9311 3.64937 19.9308 3.0162 19.5401 2.62587C19.1494 2.23554 18.5162 2.23586 18.1259 2.62658L10.4996 10.2604L8.7071 8.46789C8.31658 8.07737 7.68341 8.07737 7.29289 8.46789C6.90236 8.85842 6.90236 9.49158 7.29289 9.8821L9.79289 12.3821C9.98049 12.5697 10.2349 12.6751 10.5002 12.675C10.7655 12.6749 11.02 12.5694 11.2075 12.3818L19.5408 4.04008Z" fill="#3955FE"/>\n' +
            '</svg>'
    }

    $('.cbs').not('.no-icheck').each(function (i, elem) {
        if ($(elem).parent().hasClass('iradio') || $(elem).parent().hasClass('icheckbox')) {
            return
        } else {
            var type = $(elem).attr('data-type') === 'toggle' ? 'itoggle' : 'icheckbox'
            var icon = type !== 'itoggle' ? templates[$(this).attr('type')] : ''
            var name = $(elem).attr('data-name') ? '<div class="fc__name">' + $(elem).attr('data-name') + '</div>' : ''
            $(elem).iCheck({
                labelHover: false,
                cursor: true,
                insert: '<div class="fc__icon">' + icon + '</div>' + name,
                checkboxClass: type
            });
        }
    })
}

function initScroll(container) {
    var perfectScrollbarOptions = {
        scrollYMarginOffset: 40,
        suppressScrollX: true,
        maxScrollbarLength: 100
    }

    if (container.hasClass('ps-container')) {
        container.perfectScrollbar('update')
    } else {
        container.perfectScrollbar(perfectScrollbarOptions)
    }
}

function searchTop(obj) {
    if (obj.elem('input').val() !== '') {
        AddObject(obj);
    }
}

function resetForm(obj) {
    var inputs = obj.find('.field__input')
    var inputSelect = obj.find('.field_select');
    var cbs = obj.find('.cbs')
    inputs.each(function (i, elem) {
        $(elem).val($(elem).attr('data-default-value'))
        $(elem).removeClass('focused')
    })

    $('.textfield__field').val('')
    inputSelect.find('.field__input').html('')
    inputSelect.find('.input-vv').val('')
    cbs.prop('checked', false)

    if (obj.hasClass('order-form')) {
        resetOrderForm(obj)
    }
}

function resetOrderForm(obj) {
    var features = obj.find('.router-features')
    var cbs = obj.find('.cbs')
    features.slideUp(300)
    features.find('.router-length').html('')
    features.find('.router-time').html('')
    features.find('input[name="router-length"]').val('');
    features.find('input[name="router-time"]').val('');
    obj.find('input[name="car_id"]').val('')
    obj.find('.select_2').each(function (i, elem) {
        $(elem).val(null).trigger('change')
        $(elem).attr('data-coord', '')
        $(elem).next().removeClass('select2-container--below').removeClass('select2-container--above')
    })
    obj.find('.order-type__slide').each(function (i, elem) {
        $(elem).find('input[type="radio"]').prop('disabled', true).prop('checked', false)
        $(elem).find('input[type="radio"]').val('')
        $(elem).find('.order-type__slide-price').remove()
    })
    obj.find('.button.submit').addClass('disabled')
    initMap()
}


function beforeConfirmOrderForm(response, ajax_config, textStatus, jqXHR) {
    if (response.status) {
        for (var ajaxConfigKey in ajax_config.data) {
            if (ajaxConfigKey !== 'action') {
                $('#modal_confirm .form').find('.element[name="' + ajaxConfigKey + '"]').val(ajax_config.data[ajaxConfigKey]);
            }
        }
        openInlineModal('#modal_confirm')
    }
}


function profileSaveDone(response, ajax_config, textStatus, jqXHR) {
    if (response.status) {
        ajax_config.options.form_obj.find('.field__input').prop('disabled', true)
        ajax_config.options.form_obj.find('.js-input-save').removeClass('show').addClass('hide')
        ajax_config.options.form_obj.find('.js-input-active').removeClass('hide').addClass('show')
    }
}
