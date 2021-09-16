// Return a helper with preserved width of cells
var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};



$(function() {
    //Paging

    // per_page
    $( document ).on( "click", ".table_data_navigation .dropdown_perpage a", function(e) {
        e.preventDefault();
        $('.table_data_navigation .per_page_value').html($(this).attr('data-value'));
        Cookies.set('c_per_page', $(this).attr('data-value'));
        $('.table_data_navigation .per_page_value').attr('data-value',$(this).attr('data-value'));
        $('.table_data_navigation .pages span').removeClass('current_page');
        $('.table_data_navigation .pages span:first').addClass('current_page');
        FilterTableData(1,$(this));
    });

    // select page
    $( document ).on( "click", ".table_data_navigation .pages a", function(e) {
        e.preventDefault();
        var obj=$(this).parent();
        if (obj.hasClass('middle')==false) {
            FilterTableData(obj.attr('data-value'),obj);
        }
    });

    // Filter
    $( document ).on( "change keyup", ".table_data [data-type='filter_value']", function() {
        FilterTableData(1,$(this));
    });



    // date selector
    $( "#user_date_from" ).datepicker({
        firstDay: 1,
        changeMonth: true,
        onClose: function( selectedDate ) {
            $( "#user_date_to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#user_date_to" ).datepicker({
        firstDay: 1,
        changeMonth: true,
        onClose: function( selectedDate ) {
            $( "#user_date_from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });

    // Plugins
    $('textarea.js-auto-size').textareaAutoSize();
    $('select.select2').select2();
    if ($( ".date_from" )) {

        $(".date_from").datepicker({
            firstDay: 1,
            changeMonth: true,
            onClose: function (selectedDate) {
                $(".date_to").datepicker("option", "minDate", selectedDate);
            }
        });
    }
    if ($( ".date_to" )) {
        $(".date_to").datepicker({
            firstDay: 1,
            changeMonth: true,
            onClose: function (selectedDate) {
                $(".date_from").datepicker("option", "maxDate", selectedDate);
            }
        });
    }
    if ((".sortable_items").length>0) {
        $('.sortable_items').sortable({
            appendTo: "body",
            helper: "clone",
            opacity: 0.8,
            update: function () {
                var sortable_data = $(this).sortable("toArray", {attribute: 'data-id'});
                SavePhotosSort(sortable_data,$(this).attr('data-sort-name'));
            }
        });
        $(".sortable_items").disableSelection();
    }

    $(".fancybox").fancybox({
        fitToView	: false,
        width		: '80%',
        height		: '90%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });




    $( document ).on( "click", ".table_data .delete,ol.sortable  .delete", function() {
        if (confirmDelete()) {
            DeleteObject($(this));
        }
    });



    // Translit url
    $( document ).on( "change", ".form .translit_from", function() {
        var to=$(this).closest('.form').find('.translit_to');
        if (to.val()=='') {
            $(this).liTranslit({'status':false,'elAlias': to,'reg':'" "="-",":"="","«"="","»"=""'});
            $(this).liTranslit('enable');
        }
    });
    $( document ).on( "keyup", ".form .translit_from", function() {
        var to=$(this).closest('.form').find('.translit_to');
        if (to.val()=='') {
            $(this).liTranslit({'status':false,'elAlias': to,'reg':'" "="-",":"="","«"="","»"=""'});
            $(this).liTranslit('enable');
        }
    });
    $( document ).on( "focus", ".form .translit_to", function() {
        $('.form .translit_from').liTranslit('disable');
    });



    $('.open_fancy').fancybox({
        padding: 0,
        width:'80%',
        height:'80%',
        type:"iframe",
        helpers: {
            overlay: {
                locked: false
            }
        }
    });

    // Upload image
    $('.upload_image_button').fileupload({
        timeout: 3000000,
        sequentialUploads:true,
        dataType: 'json',
        submit: function (e, data) {
            StartAjaxLoading();
        },
        progressall: function (e, data) {
            $('#progress').show();
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .bar').css(
                'width',
                progress + '%'
            );
        },
        done: function (e, data) {
            StopAjaxLoading();
            if (data.result.error) {
                ShowError(data.result.error);
            }
            else{
                $('#progress .bar').css('width', '0px');
                if (FilemanagerUploaded(data,$(this))) {

                }
                else if (CheckMultiplyFileUploaded(data,$(this))) {
                    $(this).closest('.upload_container').find('.images_list').append(data.result.html);

                }
                else {
                    $(this).closest('.upload_container').find('.images_list').html(data.result.html);
                }

            }
        }
    });
    $('.upload_image_button').bind('fileuploadsubmit', function (e, data) {
        if ($('.add_product_form').length>0) {
            var gg = jQuery.parseJSON($('.add_product_form').find('.fileinput-button input').attr('data-form-data'));
            data.formData = gg;
            if (!data.formData) {
                data.context.find('button').prop('disabled', false);
                input.focus();
                return false;
            }
        }
    });

    $(document).on("click","#filemanager .uploaded_image_block ",function(){
        if (parent.$.fancybox.isOpen) {
            var file_id=$(this).attr('data-id');
            var input_name=$(this).closest('#filemanager').attr('data-input');
            var multi=$(this).closest('#filemanager').attr('data-multi');
            parent.SetSelectedImage(file_id,input_name,multi);
            parent.$.fancybox.close();
        }
        else  if ( top.tinymce.activeEditor) {
            var item_url = $(this).find('img').attr("data-url");
            var args = top.tinymce.activeEditor.windowManager.getParams();
            win = (args.window);
            input = (args.input);
            win.document.getElementById(input).value = item_url;
            top.tinymce.activeEditor.windowManager.close();
        }
    });



    $( document ).on( "click", ".uploaded_image_block  .remove", function() {
        if ($(this).closest('#filemanager ').length>0) {
            DeleteImage($(this).attr('data-file_id'), $(this));
        }
        else {
            $(this).closest('.uploaded_image_block').remove();
        }
    });

    // Routes list order
    if ( $('.routes_table .sortable').length>0) {
        var routes_list = $('.routes_table .sortable').nestedSortable({
            forcePlaceholderSize: true,
            handle: 'div.handle',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            isTree: true,
            expandOnHover: 700,
            startCollapsed: false,
            stop: function () {
                SaveRoutesOrder();
            }
        });
    }

    // Ordered list collapse
    $(document).on('click','.sortable .mjs-nestedSortable-expanded .fa-caret-down', function() {
        $(this).closest('li').removeClass('mjs-nestedSortable-expanded').addClass('mjs-nestedSortable-collapsed');
        $(this).closest('li').find('fa-caret-down').hide();
        $(this).closest('li').find('fa-caret-right').show();
    });
    $(document).on('click','.sortable .mjs-nestedSortable-collapsed .fa-caret-right', function() {
        $(this).closest('li').removeClass('mjs-nestedSortable-collapsed').addClass('mjs-nestedSortable-expanded');
        $(this).closest('li').find('fa-caret-down').show();
        $(this).closest('li').find('fa-caret-right').hide();
    });

    // add routes lines
    if ($('#routes_data').length>0) {
        AddRoutes(routes_data);
    }

    // Add route rule (static)
    $( document ).on( "click", ".add_rules .stat", function() {
        AddRouteRule('static');
    });

    // Add route rule (dynamic)
    $( document ).on( "click", ".add_rules .dyn", function() {
        AddRouteRule('dynamic');
    });

    // Update regexp
    $( document ).on( "change keyup", ".regexp_data [name='rule'],.routes_add_form [name='parent_id'] ", function() {
        UpdateRegexp ();
    });


    // Delete rule
    $( document ).on( "click", ".regexp_data .delete_icon", function() {
        $(this).closest('.line').remove();
        UpdateRegexp ();
        var options={
            'BeforeFunc':GetRegExpData
        };
    });

    // Load
    $( document ).on( "change", ".routes_add_form [name='module']", function() {
        GetRouteTemplates($(this).val());
        GetRouteActions($(this).val());
    });

    /*
     Add/edit route
     */
    $( document ).on( "click", ".routes_add_form .submit2:not(.disabled)", function() {
        var options={
          'BeforeFunc':GetRegExpData
        };
        SendForm($(this).closest('.routes_add_form'),options);
    });

    $( document ).on( "click", ".navigation li.list>a", function(e) {
        e.preventDefault();
        if ($(this).parent().hasClass('active')) {
            $(this).parent().removeClass('active');
        }
        else {
            $(this).parent().addClass('active');
        }
    });


    $( document ).on( "click", ".multiline_selector", function() {
        var obj=$( ".multiline_container[data-value='"+$(this).attr('data-value')+"']" );
        obj.find('.multiline_data').append( obj.find('.multiline_template').clone().wrap('<div>').parent().html());
    });

    $(document).on('click','.multiline_data .multiline_template .delete_icon', function(e) {
        $(this).closest('.multiline_template').remove();
    });

    $(document).on('click','.print_action', function(e) {
        window.print();
    });

    $('.table_content.sortable').sortable(
        {
            cursor: "move",
            helper: fixHelper,
            update: function () {
                var sortable_data = $(this).sortable("toArray", {attribute: 'data-id'});
                SaveTableSort(sortable_data,$(this).attr('data-name'));
            }
        }
    ).disableSelection();


    if ( $('.cats_sort .sortable').length>0) {
        var cats_list = $('.cats_sort .sortable').nestedSortable({
            forcePlaceholderSize: true,
            handle: 'div.handle',
            helper: 'clone',
            items: 'li',
            startCollapsed:true,
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            isTree: true,
            expandOnHover: 700,
            stop: function (event, ui) {
                SaveCatsOrder(ui.item.attr('data-id'));
            }
        });
    }
    if ( $('.settings_sort .sortable').length>0) {
        var settings_list = $('.settings_sort .sortable').nestedSortable({
            forcePlaceholderSize: true,
            handle: 'div.handle',
            helper: 'clone',
            items: 'li',
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            isTree: true,
            expandOnHover: 700,
            startCollapsed: false,
            stop: function (event, ui) {
                SaveSettingsFieldsOrder(ui.item.attr('data-id'));
            }
        });
    }
    $( document ).on( "keyup", ".product_gab_action", function() {
        ChangeProductGab($(this));
    });
    $( document ).on( "keyup", ".product_amount_action", function() {
        ChangeProductAmount($(this));
    });
    $( document ).on( "click", ".object_status span", function() {
        ChangeObjectStatus($(this).parent());
    });
    $( document ).on( "click", ".badge_status span", function() {
        ChangeBadgeStatus($(this).parent());
    });
    $( document ).on( "click", ".status_choose a", function(e) {
        e.preventDefault();
        ChangeOrderStatus($(this));

    });
  $( document ).on( "click", ".status_choose2  a", function(e) {
    e.preventDefault();
    ChangeOrderItemStatus($(this));

  });

    jQuery.datetimepicker.setLocale('ru');
    jQuery('.datetimepicker').datetimepicker({
        format:'d.m.Y H:i',
        timepicker:true,
        dayOfWeekStart:1
    });



    $( document ).on( 'click', '.update_product_amount', function() {
        var data={};
        data['action']='update';

        SendAjaxRequest(
            {
                'data': data
            }
        );
    });



    $( document ).on( 'click', '.show_cat_sort', function() {
        if ($(this).attr('data-status')=='true') {
            $('.per_page_value').attr('data-value',20);
            $('[data-type="filter_value"][data-name="order"]').val('name');
            $('.sort_block').show();
            $('.show_cat_sort span').addClass('hidden');
            $('.show_cat_sort span:first-child').removeClass('hidden');
            $('.sort_cat_element').removeClass('active');
            $('[data-name="sort_filter"]').val('title');
            $(this).attr('data-status','false');
            FilterTableData(1,$('.table_data_navigation'));
        }
        else {
            $('.per_page_value').attr('data-value',10000);
            $('[data-type="filter_value"][data-name="order"]').val('sort');
            $('.sort_block').hide();
            $('.show_cat_sort span').addClass('hidden');
            $('.show_cat_sort span:last-child').removeClass('hidden');
            $('.sort_cat_element').addClass('active');
            $(this).attr('data-status','true');
            $('[data-name="sort_filter"]').val(1);
            FilterTableData(1,$('.table_data_navigation'));
        }

    });

    $( document ).on( "change", ".cats_tree_select>select", function(e) {
        GetSubCatSelect($(this));
    });


    $( document ).on( "change keyup", ".products_translit_vendor, .products_translit_title ", function() {
        var to=$(this).closest('.form').find('.products_translit_url');
        to.val($.fn.liTranslit({string: $('.products_translit_vendor option:selected').text(), 'reg':'" "="-",":"=""'}).text()+'-'+$.fn.liTranslit({string: $('.products_translit_title').val(), 'reg':'" "="-",":"=""'}).text());
        $(this).liTranslit('enable');

    });

    if ( $('.cats_filters.sortable').length>0) {
        $('.cats_filters.sortable').sortable({
            appendTo: "body",
            helper: "clone",
            opacity: 0.8,
            update: function () {
                var sortable_data = $(this).sortable("toArray", {attribute: 'data-id'});
                SaveCatsFiltersOrder(sortable_data);
            }
        });
        $(".cats_filters.sortable").disableSelection();
    }

  $( document ).on( "change", ".vendor-collection", function(e) {
      var data={};
      data['action']='get_collections';
      data['vendor_id']=$(this).val();
      var options={};
      options['insert_elem']=$('[name="collection_id"]');
      SendAjaxRequest(
        {
          'data': data,
          'options':options
        }
      );

  });

  $( document ).on( "blur", ".table_price_input, .table_price2_input, .table_amount_input", function(e) {
    var data={};
    data['action']='change_item_values';
    data['item_id']=$(this).attr('data-id');
    data['price']=$('[data-id="'+$(this).attr('data-id')+'"].table_price_input').val();
    data['price_old']=$('[data-id="'+$(this).attr('data-id')+'"].table_price2_input').val();
    data['price_val']=$('[data-id="'+$(this).attr('data-id')+'"].table_price3_input').val();
    data['amount']=$('[data-id="'+$(this).attr('data-id')+'"].table_amount_input').val();

    var options={};

    SendAjaxRequest(
      {
        'data': data,
        'options':options
      }
    );

  });

  $( document ).on( "click", ".gen-desc", function(e) {
      e.preventDefault();
    var data=jQuery.parseJSON(desc_list);
    var value=randomProperty(data);
    $('.gen-value').val(value['value']);
  });
  $( document ).on( "change", ".mass-sprav", function(e) {
    var data={};
    data['action']='mass-sprav';
    data['value']=$(this).val();

    var options={};
    options['insert_elem']=$('.mass-sprav2');
    options['show_and_hide']=3;

    SendAjaxRequest(
      {
        'data': data,
        'options': options
      }
    );
  });

  $( document ).on( "click", ".mass-check-all", function(e) {
        if ($(this).is(':checked')) {
            $('.mass-check-one').prop('checked',true);
        }
        else {
          $('.mass-check-one').prop('checked',false);
        }
  });



  $(document).on("change", ".change", function () {

    var data_array = {};
    var obj = $(this).closest('.form')
    obj.find(".change").each(function (i, elem) {
      data_array[$(elem).attr('name')] = $(elem).val()
    });

    data_array['action'] = 'process_getPriceAdmin';
    var options = {};
    options['AfterDone'] = AfterChangeOrder;

    SendAjaxRequest(
      {
        'url': home_url + '/manager/taxi/orders/add/',
        'data': data_array,
        'options': options,
      }
    );
  });

  function AfterChangeOrder(response, ajax_config, textStatus, jqXHR) {
    if (response.status) {
      $('input[name="price"]').val(response.price['price_value']);
      $(".distance_div").show();
    } else {
      $('input[name="price"]').val("");
      $(".price_div").show();
      $(".distance_div").show();
    }
  }



  $( document ).on( "click", ".update-all-price", function(e) {
    var data={};
    data['action']='mass-price';

    data['price_change']=$('.mass-price-update').val();
    data['price_type']=$('.mass-price-type').val();
    data['price_class_id']=$('.mass-price-class').val();

    var options={};
    options['obj']=$('[data-name="item_price_min"]');
    options['show_and_hide']=3;

    SendAjaxRequest(
      {
        'data': data,
        'options': options
      }
    );
  });


} );


