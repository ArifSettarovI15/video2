$.xhrPool = [];
$.xhrPool.abortAll = function() { // our abort function
    $(this).each(function(idx, jqXHR) {
        jqXHR.abort();
    });
    $.xhrPool.length = 0
};
var start_ajax_time=0;
var end_ajax_time=0;
function AbortAjaxRequest(jqXHR) {
    $.xhrPool.abortAll();
    $.xhrPool.push(jqXHR);
}
function RemoveAjaxRequest(jqXHR) {
    var index = $.xhrPool.indexOf(jqXHR);
    if (index > -1) {
        $.xhrPool.splice(index, 1);
    }
}
function StartAjaxLoading() {
    start_ajax_time=new Date().getTime();
    $('.ajax-loading').mod('show',true);
    $('body').addClass('body-shadow');
}
function StopAjaxLoading() {
    var obj=$('.ajax-loading');
    var min_time=500;
    if (start_ajax_time) {
        end_ajax_time=new Date().getTime()-start_ajax_time;
        if (end_ajax_time<min_time) {
            setTimeout(function () {
                CloseAjaxLoader(obj);
            }, (min_time-end_ajax_time));
        }
        else {
            CloseAjaxLoader ()
        }
    }
    else {
      CloseAjaxLoader ();
    }

}
function CloseAjaxLoader () {
    $('.ajax-loading').mod('show',false).mod('center', false);
    $('body').removeClass('body-shadow');
}
function CloseAjaxLoader2 () {
    $('.ajax-loading').mod('show2',false).mod('center',false);
    $('body').removeClass('body-shadow');
   // $('.mfp-wrap').show();
   // $('.mfp-bg').show();
}
function StopAjaxLoading2() {
    var obj=$('.ajax-loading');
    var min_time=500;
    if (start_ajax_time) {
        end_ajax_time=new Date().getTime()-start_ajax_time;
        if (end_ajax_time<min_time) {
            setTimeout(function () {
                CloseAjaxLoader2(obj);
            }, (min_time-end_ajax_time));
        }
        else {
            CloseAjaxLoader2 ()
        }
    }
    else {
        CloseAjaxLoader2 ()
    }

}
function StartAjaxLoading2() {
  start_ajax_time=new Date().getTime();
  $('.ajax-loading').mod('show2',true).mod('center',false);
}
function SendAjaxRequest(obj) {
    var ajax_config={
        method:'POST',
        data:{},
        options:{},
        url:'',
        dataType:'json',
        onlyOne:false,
        ShowLoading:true,
        ShowLoading2:false,
        statusCode: {
            401: function() {
                window.location.reload();
            }
            /*,404: function() {
             ShowError (JsLang.ajax.not_found);
             }*/
        },
        onBefore:function(ajax_config,jqXHR){},
        onComplete:function(response,ajax_config,textStatus,jqXHR){},
        onDone:AjaxRequestDone,
        onError:AjaxRequestError
    };
    $.each(obj, function( index, value ) {
        ajax_config[index]=value;
    });

    if (ajax_config.options['form_obj']) {
        if (ajax_config.options['form_obj'].attr('data-insert')) {
            if (ajax_config.options['form_obj'].attr('data-insert')=='form_obj') {
                ajax_config.options['insert_elem'] = ajax_config.options['form_obj'];
            }
            else {
                ajax_config.options['insert_elem'] = $(ajax_config.options['form_obj'].attr('data-insert'));
            }
          if (ajax_config.options['form_obj'].attr('data-scroll')) {
            $("html, body").animate({scrollTop: ajax_config.options['form_obj'].offset().top - 60}, 750);
          }
        }
      if (ajax_config.options['form_obj'].attr('data-type')===1) {
        ajax_config.ShowLoading=true;
        ajax_config.ShowLoading2=false;
      }
      if (ajax_config.options['form_obj'].attr('data-type')===2) {
        ajax_config.ShowLoading2=true;
        ajax_config.ShowLoading=false;
      }

        if (ajax_config.options['form_obj'].attr('data-one')=='1') {
            ajax_config.onlyOne=true;
        }
        if (ajax_config.options['form_obj'].attr('data-callback')) {
            ajax_config.options['AfterDone']=window[ajax_config.options['form_obj'].attr('data-callback')];
        }
    }
    $.ajax({
        method: ajax_config.method,
        url: ajax_config.url,
        data: ajax_config.data,
        dataType: ajax_config.dataType,
        beforeSend: function (jqXHR) {
            if (ajax_config.onlyOne) {
                AbortAjaxRequest(jqXHR);
            }
            if (ajax_config.ShowLoading) {
                StartAjaxLoading();
            }
            else if (ajax_config.ShowLoading2) {
                StartAjaxLoading2();
            }
            ajax_config.onBefore(ajax_config,jqXHR);
        }
    })
        .done(function( response,textStatus,jqXHR ) {
                ajax_config.onDone(response,ajax_config,textStatus,jqXHR );
            }
        )
        .fail(function( response,textStatus,jqXHR ) {
                ajax_config.onError(response,ajax_config,textStatus,jqXHR );
            }
        )
        .always(function( response,textStatus,jqXHR ) {
                if (ajax_config.onlyOne) {
                    RemoveAjaxRequest(jqXHR);
                }
                if (ajax_config.ShowLoading) {
                    StopAjaxLoading();
                }
                  if (ajax_config.ShowLoading2) {
                    StopAjaxLoading2();
                  }
                ajax_config.onComplete(response,ajax_config,textStatus,jqXHR );
            }
        );
}

function AjaxRequestError  (response,ajax_config,textStatus,jqXHR ) {
  if (textStatus=='parsererror') {
    ShowError ('Ошибка парсинга');
  }
  else if (textStatus=='timeout') {
    ShowError ('Таймаут');
  }
  else if (textStatus=='abort') {

  }
  else {
    ShowError ('Ошибка');
  }
}

function AjaxRequestDone(response,ajax_config,textStatus,jqXHR) {
    var options=ajax_config.options;

    var data=response;
    if (options.BeforeDone) {
        options.BeforeDone(response,ajax_config,textStatus,jqXHR);
    }

  if (data.redirect) {
    window.location.href=data.redirect;
    return;
  }

    if (options.insert_elem) {
        options.insert_elem.html(data.html);
    }
    if (options.append_elem) {
        options.append_elem.append(data.html);
    }
    if (options.delete_elem) {
        options.delete_elem.remove();
    }
    ShowAjaxMessage(data,options);

    if (options.AfterDone) {
        options.AfterDone(response,ajax_config,textStatus,jqXHR);
    }
    if (response.reload || options.reload) {
        window.location.reload();
    }
}

function ShowAjaxMessage(data,options) {
    if (data.status) {
        if (data.text) {
            if (data.message_inline && options.insert_elem) {
                ShowInlineMessage(data.text, options.insert_elem);
            }
            else if (options.show_and_hide){
                ShowNotificationAndHide(data.text,'message',options.show_and_hide);
            }
            else {
                ShowMessage(data.text);
            }
        }
    }
    else {
        if (data.text) {
            if (data.message_inline && options.insert_elem) {
                ShowInlineError(data.text, options.insert_elem);
            }
            else if (options.show_and_hide){
                ShowNotificationAndHide(data.text,'error',options.show_and_hide);
            }
            else {
                ShowError(data.text);
            }
        }

        if (data.error_field) {
            var a=data.error_field.split(',');
            $.each(a, function( index, value ) {
                SetErrorToElement(options.form_obj.find('[name="'+value+'"]'));
            });

        }

    }
}

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

function CloseModals() {
    var magnificPopup = $.magnificPopup.instance;
    magnificPopup.close();
}

function onOpenModal() {
    initForms()
}

function openInlineModal(src, trigger) {
    var aOpen;
    var aClose;
    if (trigger) {
        aOpen = trigger.attr('data-effect');
        aClose = trigger.attr('data-effect-close');
    }
    $.magnificPopup.open({
        fixedContentPos: true,
        fixedBgPos: true,
        closeOnBgClick: true,
        overflowY: 'auto',
        showCloseBtn: false,
        preloader: false,
        midClick: true,
        items: {
            src: src,
            type: 'inline'
        },
        callbacks: {
            open: function () {
                onOpenModal();
                if (trigger && trigger.attr('data-after')) {
                    var func = window[trigger.attr('data-after')]
                    if (func) {
                        func()
                    }
                }
            },
            close: function () {
                CloseModals();
            },
            beforeOpen: function () {
                if (trigger && trigger.attr('data-before')) {
                    var func = window[trigger.attr('data-before')]
                    if (func) {
                        func(this, trigger)
                    }
                }

                initScroll($('.modal-search__list'))

                var cl = '';
                if (aOpen) {
                    cl = aOpen;
                } else {
                    cl = '';
                }
                // $('.modal').removeClass(aClose).addClass(cl + ' animated ');
            },
            beforeClose: function () {
                var cl = '';
                if (aClose) {
                    cl = aClose;
                } else {
                    cl = '';
                }
                // $('.modal').removeClass(aOpen + ' animated ').addClass(cl + ' animated ');
            },
            afterClose: function () {
                var cl = ''
                if (aClose) {
                    cl = aClose;
                } else {
                    cl = '';
                }

                // $('.modal').removeClass(cl)
            }
        }
    });
    grunticon.embedIcons(grunticon.getIcons(grunticon.href));
}

function afterCloseMainModal() {
    $($.magnificPopup.instance.st.items.src).removeClass('mfp-hide')
}

// function initModal() {
//   $('.popup-modal').magnificPopup({
//     type: 'inline',
//     preloader: false,
//     fixedContentPos: true,
//     showCloseBtn: false,
//     fixedBgPos: true,
//     removalDelay: 300,
//     overflowY: 'scroll',
//     callbacks: {
//       open: function() {
//         onOpenModal();
//       },
//
//       beforeOpen: function() {
//         clearSelects();
//         var cl = '';
//         if (this.st.el.attr('data-effect')) {
//           cl = this.st.el.attr('data-effect');
//         } else {
//           cl = 'zoomInUp';
//         }
//         $('.modal').addClass(cl + ' animated ');
//       },
//
//       close: function() {
//         CloseModals();
//       },
//
//       afterClose: function() {
//         afterCloseModals();
//       },
//
//       beforeClose: function() {
//         var cl = '';
//         if (this.st.el.attr('data-effect')) {
//           cl = this.st.el.attr('data-effect');
//         } else {
//           cl = 'zoomInUp';
//         }
//         $('.modal').removeClass(cl + ' animated fast ').addClass('fadeOut animated ');
//       }
//     }
//   });
// }

function ShowError (text) {
    ShowNotificationAndHide(text, 'error');
}

function ShowMessage (text) {
    ShowNotificationAndHide(text, 'message');
}
function ShowInlineError(text,elem) {
    ShowInlineInfo(text,elem,"error");
}
function ShowInlineMessage(text,elem) {
    ShowInlineInfo(text,elem,"message");
}
function ShowInlineInfo(text,elem,class_name) {
    class_name = class_name || "message";
    elem.html('<div class="inline_notification '+class_name+'">'+text+'</div>');
}

function ShowNotification (text,class_name) {
    class_name = class_name || "message";
    var icon;
    var iconClose = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
        '<path fill-rule="evenodd" clip-rule="evenodd" d="M15.7071 5.70711C16.0976 5.31658 16.0976 4.68342 15.7071 4.29289C15.3166 3.90237 14.6834 3.90237 14.2929 4.29289L10 8.58579L5.70711 4.29289C5.31658 3.90237 4.68342 3.90237 4.29289 4.29289C3.90237 4.68342 3.90237 5.31658 4.29289 5.70711L8.58579 10L4.29289 14.2929C3.90237 14.6834 3.90237 15.3166 4.29289 15.7071C4.68342 16.0976 5.31658 16.0976 5.70711 15.7071L10 11.4142L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L11.4142 10L15.7071 5.70711Z" fill="white"/>\n' +
        '</svg>\n'

    switch (class_name) {
        case 'error':
            icon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" clip-rule="evenodd" d="M9 2C9.43043 2 9.81257 2.27543 9.94868 2.68377L15 17.8377L17.0513 11.6838C17.1874 11.2754 17.5696 11 18 11H22C22.5523 11 23 11.4477 23 12C23 12.5523 22.5523 13 22 13H18.7208L15.9487 21.3162C15.8126 21.7246 15.4304 22 15 22C14.5696 22 14.1874 21.7246 14.0513 21.3162L9 6.16228L6.94868 12.3162C6.81257 12.7246 6.43043 13 6 13H2C1.44772 13 1 12.5523 1 12C1 11.4477 1.44772 11 2 11H5.27924L8.05132 2.68377C8.18743 2.27543 8.56957 2 9 2Z" fill="white"/>\n' +
                '</svg>\n';
            break;
        case 'message':
            icon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" clip-rule="evenodd" d="M10.0166 3.21555C11.9096 2.78783 13.8902 2.98352 15.663 3.77342C16.1675 3.9982 16.7587 3.77146 16.9834 3.26699C17.2082 2.76251 16.9815 2.17134 16.477 1.94656C14.3103 0.981129 11.8896 0.74196 9.57581 1.26472C7.26206 1.78748 5.17929 3.04416 3.63811 4.84734C2.09693 6.65052 1.17992 8.90358 1.02384 11.2705C0.86777 13.6374 1.48099 15.9914 2.77206 17.9813C4.06312 19.9713 5.96285 21.4906 8.18792 22.3126C10.413 23.1347 12.8442 23.2154 15.1189 22.5428C17.3936 21.8703 19.39 20.4804 20.8103 18.5806C22.2306 16.6807 22.9987 14.3726 23 12.0006V12V11.08C23 10.5277 22.5523 10.08 22 10.08C21.4477 10.08 21 10.5277 21 11.08V11.9994C20.9989 13.9402 20.3705 15.8286 19.2084 17.3831C18.0464 18.9375 16.413 20.0746 14.5518 20.6249C12.6907 21.1752 10.7015 21.1091 8.88102 20.4365C7.06051 19.764 5.50619 18.5209 4.44987 16.8928C3.39354 15.2646 2.89181 13.3387 3.01951 11.4021C3.14721 9.46552 3.89749 7.62211 5.15845 6.14678C6.41942 4.67145 8.12351 3.64326 10.0166 3.21555ZM22.7075 4.70674C23.0978 4.31602 23.0975 3.68286 22.7068 3.29253C22.316 2.9022 21.6829 2.90252 21.2925 3.29323L11.9997 12.5954L9.70711 10.3029C9.31659 9.91236 8.68342 9.91236 8.2929 10.3029C7.90238 10.6934 7.90238 11.3266 8.2929 11.7171L11.2929 14.7171C11.4805 14.9047 11.735 15.0101 12.0003 15.01C12.2656 15.0099 12.52 14.9044 12.7075 14.7167L22.7075 4.70674Z" fill="white"/>\n' +
                '</svg>\n';
            break;
        case 'decline':
            icon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" clip-rule="evenodd" d="M6.38231 4.9681C7.92199 3.73647 9.87499 3 12 3C16.9706 3 21 7.02944 21 12C21 14.125 20.2635 16.078 19.0319 17.6177L6.38231 4.9681ZM4.9681 6.38231C3.73647 7.92199 3 9.87499 3 12C3 16.9706 7.02944 21 12 21C14.125 21 16.078 20.2635 17.6177 19.0319L4.9681 6.38231ZM12 1C5.92487 1 1 5.92487 1 12C1 18.0751 5.92487 23 12 23C18.0751 23 23 18.0751 23 12C23 5.92487 18.0751 1 12 1Z" fill="white"/>\n' +
                '</svg>\n';
            break;
        case 'wait':
            icon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" clip-rule="evenodd" d="M8.14109 4.99636C9.66003 4.15882 11.41 3.83765 13.1274 4.08125C14.8448 4.32484 16.4364 5.12 17.6626 6.3469C17.6717 6.35608 17.6811 6.36507 17.6906 6.37389L20.5278 9.00002H16.9999C16.4476 9.00002 15.9999 9.44773 15.9999 10C15.9999 10.5523 16.4476 11 16.9999 11H22.9999C23.5522 11 23.9999 10.5523 23.9999 10V4.00002C23.9999 3.44773 23.5522 3.00002 22.9999 3.00002C22.4476 3.00002 21.9999 3.44773 21.9999 4.00002V7.63737L19.0631 4.91901C17.5323 3.39341 15.5484 2.40463 13.4083 2.10107C11.2616 1.79658 9.07407 2.19804 7.17538 3.24495C5.2767 4.29187 3.76971 5.92752 2.8815 7.90543C1.9933 9.88334 1.772 12.0963 2.25095 14.211C2.7299 16.3256 3.88316 18.2273 5.53694 19.6294C7.19072 21.0316 9.25541 21.8583 11.4199 21.9849C13.5844 22.1115 15.7314 21.5312 17.5374 20.3315C19.3434 19.1317 20.7105 17.3775 21.4328 15.3331C21.6167 14.8124 21.3437 14.2411 20.823 14.0571C20.3023 13.8732 19.731 14.1462 19.547 14.6669C18.9692 16.3024 17.8755 17.7058 16.4307 18.6656C14.9859 19.6254 13.2683 20.0896 11.5367 19.9883C9.80511 19.887 8.15335 19.2257 6.83033 18.1039C5.50731 16.9822 4.58471 15.4609 4.20154 13.7692C3.81838 12.0775 3.99542 10.3071 4.70598 8.72474C5.41655 7.14241 6.62214 5.83389 8.14109 4.99636Z" fill="white"/>\n' +
                '</svg>\n';
            break;
        default:
            icon = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
                '<path fill-rule="evenodd" clip-rule="evenodd" d="M12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3ZM1 12C1 5.92487 5.92487 1 12 1C18.0751 1 23 5.92487 23 12C23 18.0751 18.0751 23 12 23C5.92487 23 1 18.0751 1 12ZM12 11C12.5523 11 13 11.4477 13 12V16C13 16.5523 12.5523 17 12 17C11.4477 17 11 16.5523 11 16V12C11 11.4477 11.4477 11 12 11ZM12 7C11.4477 7 11 7.44772 11 8C11 8.55228 11.4477 9 12 9H12.01C12.5623 9 13.01 8.55228 13.01 8C13.01 7.44772 12.5623 7 12.01 7H12Z" fill="#424242"/>\n' +
                '</svg>\n';
            break;
    }

    $('#notification').remove();
    $( '<div id="notification" class="animated bounceInDown '+class_name+'"><div class="content"><div class="icon">' + icon +'</div>' + '<div class="text">' + text + '</div></div><div class="close">' + iconClose + '</div></div>' ).insertAfter( $( "body" ) );
}

function HideNotification () {
    $("#notification").removeClass('animated bounceInDown');
    $("#notification").addClass('animated bounceOutUp');
}
var notification_id;
function ShowNotificationAndHide (text,class_name,sec) {
    class_name = class_name || "message";
    sec = sec || 3;
    var hide_time=sec*1000;

    ShowNotification (text,class_name);
    clearTimeout(notification_id)
    notification_id=setTimeout(
        function()
        {
            HideNotification();
        }, hide_time);
}

function destroySlide(slider) {
    slider.each(function (a, b) {
        $(this).slick('unslick');
    });
};

function slideLavaDots(slider) {
    var lavalampOptions = {
        autoUpdate: true,
        easing: 'easeInOutCubic'
    }
    var lava = false
    return function() {
        var $dots = $('.slick-dots')

        slider.on('init', function(event, slick) {
            $dots = slick.$dots
            if ($dots) {
                $dots.each(function(i, elem) {
                    if (!$(elem).hasClass('lavalamp')) {
                        $(elem).lavalamp(lavalampOptions)
                    }
                })
                lava = true

            }

            $(window).on('orientationchange resize', function(event) {
                if (lava) {
                    $dots.each(function(i, elem) {
                        if ($(elem).hasClass('lavalamp')) {
                            $(elem).lavalamp('destroy')
                        }
                    })
                    lava = false
                }
            })
        })

        // slider.on('breakpoint', function(event, slick, breakpoint) {
        //     $dots = slick.$dots
        //     if ($dots.hasClass('lavalamp')) {
        //         $dots.lavalamp('update')
        //     }
        // })

        slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            $dots = slick.$dots
            if ($dots.hasClass('lavalamp')) {
                var a =  $dots.children().eq(nextSlide)
                $dots.data('lavalampActive', a).lavalamp('update');
            }
        })
    }
}


function TabsSet(item) {
    var tab_block=item.block();
    var tabs_block_id=tab_block.attr('data-tab');

    var tab_id=item.attr('data-id');
    var tabs_content=$('.tab-content[data-tab="'+tabs_block_id+'"]');

    tab_block.elem('link').delMod('active');
    item.mod('active', true);

    tabs_content.elem('content').delMod('active').removeClass('fadeIn').addClass('fadeOut');
    tabs_content.elem('content').filter( '[data-id="'+tab_id+'"]' ).mod('active',true).removeClass('fadeOut').addClass('fadeIn');

    if (tab_block.attr('data-after')) {
        window[tab_block.attr('data-after')]()
    }
}


function openTab(tab,item_id) {
  var tabs_block_id=tab;
  var tab_block=$('.tabs[data-tab="'+tab+'"]');

  var tab_id=item_id;
  var tabs_content=$('.tabs-content[data-tab="'+tabs_block_id+'"]');

  tab_block.elem('item').delMod('active');
  tab_block.find('[data-id="'+item_id+'"].tabs__item').mod('active',true);

  tabs_content.elem('content').delMod('active');
  tabs_content.elem('content').filter( '[data-id="'+tab_id+'"]' ).mod('active',true);
}

function initSliderTabs() {
    var container = $('.tabs__inner')
    if (container.length > 0) {
        var options = {
            horizontal: 1,
            itemNav: 'basic',
            smart: 1,
            activateOn: 'click',
            mouseDragging: 1,
            touchDragging: 1,
            releaseSwing: 1,
            startAt: 0,
            scrollBy: 1,
            activatePageOn: 'click',
            speed: 300,
            elasticBounds: 1,
            dragHandle: 1,
            dynamicHandle: 1,
            clickBar: 1,
            slidee: container.find('.tabs__list'),
            itemSelector: container.find('.tabs__item').eq(0),
        };

        container.each(function(i, elem) {
            new Sly(elem, options).init()
        })
    }
}

initSliderTabs()
