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
