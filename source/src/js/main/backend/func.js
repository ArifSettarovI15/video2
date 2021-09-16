function FilterTableData(page,obj) {
    if (obj.attr('data-before')) {
        var func=window[obj.attr('data-before')];
        func(obj);
    }
    var data_array={};
    if (page % 1 === 0) {

    }
    else {
        page=1;
    }
    data_array['page']=page;
    data_array['per_page']=$('.per_page_value ').attr('data-value');
    obj.closest('.table_data').find('[data-type="filter_value"]').each(function(index,value) {
        if (($(this).attr('data-no-empty') && $(this).val()!=0 && $(this).val()!='') ||
            !$(this).attr('data-no-empty')) {
            data_array[$(this).attr('data-name')] = $(this).val();
        }
    });

    var options={};
    options['obj']=obj.closest('.table_data');
    options['AfterDone']=FilterTableDataDone;
    SendAjaxRequest(
        {
            'data':data_array,
            'options':options,
            'onlyOne':true
        }
    );
}
var randomProperty = function (object) {
  var keys = Object.keys(object);
  return object[keys[Math.floor(keys.length * Math.random())]];
};
function SwitchTab (item) {
  var tab_block=item.closest('.tabs_block');
  var tabs_block_id=tab_block.attr('data-value');
  var tab_id=item.attr('data-id');
  var tabs_content=$('.tabs_content[data-selector="'+tabs_block_id+'"]');

  tab_block.find('.tabs_switcher').removeClass('active');
  item.addClass('active');
  tabs_content.children().removeClass('active');
  tabs_content.children('[data-tab="'+tab_id+'"]').addClass('active');
}
function FilterTableDataDone(response,ajax_config,textStatus,jqXHR) {
    ajax_config['options']['obj'].find('.table_content').html(response.html)
    ajax_config['options']['obj'].find('.table_data_navigation').html(response.paging_old);
}

// Routes
function SaveRoutesOrder () {
    var tree = $('ol.sortable').nestedSortable('toHierarchy', {startDepthCount: 0});
    var data={};

    data['tree']=PrepareOrderData(tree);
    data['action']='UpdateSort';

    var options={};
    options['show_and_hide']=3;
    SendAjaxRequest(
        {
            'data':data,
            'options':options
        }
    );

}




function DeleteRoute(id) {
    var data={};
    data['action']='DeleteRoute';
    data['id']=id;
    var options={};
    options['BeforeDone']=DeleteRouteDone;

    SendAjaxRequest(
        {
            'data':data,
            'options':options
        }
    );
}

function DeleteRouteDone(response,ajax_config,textStatus,jqXHR) {
    $('#sort_id_'+ajax_config.data.id).remove();
}

function AddRouteRule(type) {
    $( ".regexp_data" ).append( $('.regexp_templates> .'+type).clone().wrap('<div>').parent().html());
    UpdateRegexp ();
}
function  GetRouteTemplates (module){
    var data={};
    data['action']='GetTemplates';
    data['module']=module;

    SendAjaxRequest(
        {
            'data':data,
            'onDone':InsertRouteTemplate
        }
    );
}

function InsertRouteTemplate(data) {
    var html='<option value="">Без шаблона</option>';
    $(data.list).each(function(index,value) {
        html=html+'<option value="'+value+'">'+value+'</option>';
    });
    $('.routes_add_form [name="template"]').html(html);
}

function  GetRouteActions (module){
    var data={};
    data['action']='GetActions';
    data['module']=module;

    SendAjaxRequest(
        {
            'data':data,
            'onDone':InsertRouteAction
        }
    );
}
function InsertRouteAction(data) {
    var html='<option value="">Без действия</option>';
    $(data.list).each(function(index,value) {
        html=html+'<option value="'+value+'">'+value+'</option>';
    });
    $('.routes_add_form [name="route_action"]').html(html);
}

function  UpdateRegexp (){
    var html='';
    if ($('.routes_add_form [name="parent_id"]').val()!='') {
        $('#parent_route').html($('.routes_add_form [name="parent_id"] option:selected').text()+'/');
    }

    $('.regexp_data [name="rule"]').each(function(index,value) {
        if ($(this).val()!='') {
            html = html + $(this).val() + '/';
        }
    });
    html=html.slice(0,-1);
    $('#rule_value').html(html);
    $('.routes_add_form [name="rule_value"]').html(html);

}

function GetRegExpData() {
    var values=[];
    $($('.regexp_data .line')).each(function(index,value) {
        var item_value={
            'rule':$(this).find('[name="rule"]').val(),
            'type':$(this).find('[name="type"]').val(),
            'name':$(this).find('[name="request_name"]').val(),
            'value':$(this).find('[name="request_value"]').val(),
            'static':$(this).find('[name="static"]').val(),
            'pos':$(this).find('[name="pos"]').val()
        };
        values.push(item_value);
    });
    $('.routes_add_form [name="rules"]').val(JSON.stringify( values ));
}

function AddRoute(form_obj) {
    var data=GetFormData(form_obj);
    var options={};
    options['form_obj']=form_obj;
    options['insert_elem']=form_obj;
    if (CheckForm(form_obj)) {
        SendAjaxRequest(
            {
                'data':data,
                'options':options,
                'onComplete':FormComplete,
                'onBefore':FormBefore
            }
        );
    }
}

function AddRoutes(data) {
    $.each(data, function( index, value ) {
        var type='static';
        if (value.static==0) {
            type='dynamic';
        }
        AddRouteRule(type);
        $('.regexp_data .line:last-child [name="rule"]').val(value.rule);
        $('.regexp_data .line:last-child [name="request_name"]').val(value.name);
        $('.regexp_data .line:last-child [name="pos"]').val(value.pos);
        if (value.static==1) {
            $('.regexp_data .line:last-child [name="request_value"]').val(value.value);
        }
        else {
            $('.regexp_data .line:last-child [name="type"]').val(value.type);
        }
    });
    UpdateRegexp ();
}

// Global
function PrepareOrderData(data) {
    var tree_data={};
    $.each(data, function( index, value ) {
        tree_data[index]={};
        tree_data[index]['id']=value.id;
        if (value.children) {
            var d=PrepareOrderData(value.children);
            tree_data[index]['children']=d;
        }

    });
    return tree_data;
}


function confirmRecover() {
    if (confirm('Подтвердить восстановление?')) {
        return true;
    } else {
        return false;
    }
}
function confirmDelete() {
    if (confirm('Подтвердить удаление?')) {
        return true;
    } else {
        return false;
    }
}


function DeleteImage(id,obj) {
    var data={};
    data['file_id']=id;

    var options={};
    options['delete_elem']=obj.closest('.uploaded_image_block');
    options['obj']=obj.closest('.table_data');
    options['AfterDone']= DeleteImageDone;

    SendAjaxRequest(
        {
            'url':'/files/delete/',
            'data':data,
            'options':options
        }
    );
}

function DeleteImageDone(response,ajax_config,textStatus,jqXHR) {
    if (ajax_config['options']['obj'].closest('#filemanager').length>0) {
        var table_page = ajax_config['options']['obj'].find('.table_data_navigation .paging span.active');
        if (table_page.length > 0) {
            FilterTableData(table_page.attr('data-value'), ajax_config['options']['obj']);
        }
        else {
            FilterTableData(1, ajax_config['options']['obj']);
        }
    }
}



function FilemanagerUploaded(data,obj) {
    var filemanager=$('#filemanager');
    if (filemanager.length>0) {
        FilterTableData(1, filemanager.find('.table_content'));
        SwitchTab(filemanager.find('.tabs_block .tabs_switcher[data-id="2"]'));
        return true;
    }
    else {
        return false;
    }
}
function CheckMultiplyFileUploaded (data,obj) {
    var elem=obj.closest('.upload_container').find('.upload_image_button');
    if (elem.attr('multiple')) {
        return true;
    }
    else {
        return false;
    }
}

function SetSelectedImage(file_id,input_name,multi) {
    var data_array={};
    data_array['file_id']=file_id;
    data_array['input_name']=input_name;
    data_array['multiple']=multi;
    var options={};
    var key='insert_elem';
    if (multi) {
        key='append_elem';
    }
    options[key]=$('.images_list[data-list="'+input_name+'"]');
    SendAjaxRequest(
        {
            'url':'/files/get/',
            'data':data_array,
            'options':options
        }
    );
}

// Delete Element
function DeleteObject(obj) {
    var data={};
    data['action']='delete';
    data['object_id']=obj.attr('data-value');

    var options={};
    options['form_obj']=obj;
    options['AfterDone']=ObjectDeleteDone;

    SendAjaxRequest(
        {
            'data': data,
            'options':options
        }
    );
}
function ObjectDeleteDone(response,ajax_config,textStatus,jqXHR) {
    if (response.status) {
        var obj=ajax_config['options']['form_obj'];
        if ( obj.closest('.table_data').length>0) {
            obj.closest('tr').remove();
            if ( obj.closest('.table_data .paging').length>0) {
                FilterTableData($(".table_data .paging span.active").attr('data-value'), $(this));
            }
        }
        else if (obj.closest('ol.sortable').length>0) {
            obj.closest('li').remove();
            SaveSettingsFieldsOrder(0);
        }
    }

}



function GetProductBrands(obj) {
    var data={};
    data['action']='get_models';
    data['brand']=obj.val();
    var options={};
    options['insert_elem']=$('[name="options[parent_model_id]"]');
    SendAjaxRequest(
        {
            'data':data,
            'options':options
        }
    );
}

function FilterOptionsSelect(obj) {
    var table=0;
    var obj2=obj.closest('.form').find('select[name="option_id"]');
    if (obj2.length==0) {
        obj2=obj.closest('.table_data').find('[data-name="option_id"]');
        if (obj2.length!=0) {
            table=1;
        }
    }
    else {
        table=2;
    }

    obj2.val(0);
    if (table==1) {
        var obj3=obj.closest('.table_data');
        FilterTableData(obj3.attr('data-value'), obj3.find(".table_content"));
    }

    if (obj.val()=='') {
        DisableElement(obj2);
    }
    else {
        obj2.find('option').each(function (index, value) {
            if ($(this).attr('data-cat') == obj.val() || $(this).val()==0 ) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
        EnableElement(obj2);
    }
}
function TitleToImage(obj) {
    if (obj.closest('.add_product_form').length>0) {
        ShowHiddenUpload();
        var gg = jQuery.parseJSON(obj.closest('.add_product_form').find('.fileinput-button input').attr('data-form-data'));
        gg['filename'] = $.fn.liTranslit({string: obj.val(), reg: '"/"="_"'}).text();
        gg = JSON.stringify(gg);
        obj.closest('.add_product_form').find('.fileinput-button input').attr('data-form-data', gg);
    }
}
function BrandToImage(obj) {
    if (obj.closest('.add_product_form').length>0) {
        ShowHiddenUpload();
        var gg = jQuery.parseJSON(obj.closest('.add_product_form').find('.fileinput-button input').attr('data-form-data'));
        gg['sub_folder'] = obj.find('option:selected').attr('data-url');

        var obj2 = obj.closest('.add_product_form').find('.upload_buttons .select_file');
        if (obj2.length > 0) {
            var text = obj2.attr('href');
            var newSrc = gg['sub_folder'];
            obj2.attr('href', replaceUrlParam(text, 'sub_folder', newSrc));
        }

        gg = JSON.stringify(gg);
        obj.closest('.add_product_form').find('.fileinput-button input').attr('data-form-data', gg);
    }
}

function ShowHiddenUpload() {
    var obj=$('.add_product_form [name="options[5]"]');
    if (obj.val()==0 || $('.add_product_form [name="options[15]"]').val()=='') {
        obj.closest('.add_product_form').find('.fileinput-button span').addClass('disabled');
        obj.closest('.add_product_form').find('.fileinput-button input').prop('disabled',true);
    }
    else {
        obj.closest('.add_product_form').find('.fileinput-button span').removeClass('disabled');
        obj.closest('.add_product_form').find('.fileinput-button input').prop('disabled',false);
    }
}

function replaceUrlParam(url, paramName, paramValue){
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)')
    if(url.search(pattern)>=0){
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue
}

function SaveTableSort(list,name) {
    var data={};
    data['action']='sort_table';
    data['data_sort']=list;
    data['sort_id']=name;
    var p=$('.table_data .page.active');
    if (p.length>0) {
        data['page'] = p.attr('data-value');
    }
    SendAjaxRequest(
        {
            'data': data
        }
    );
}

function SaveCatsOrder (changed_id) {
    var tree = $('ol.sortable').nestedSortable('toHierarchy', {startDepthCount: 0});
    var data={};

    data['tree']=PrepareOrderData(tree);
    data['action']='UpdateSort';
    data['changed_id']=changed_id;
    SendAjaxRequest(
        {
            'data':data
        }
    );

}

function SaveSettingsFieldsOrder (changed_id) {
    var tree = $('ol.sortable').nestedSortable('toHierarchy', {startDepthCount: 0});
    var data={};

    data['tree']=PrepareOrderData(tree);
    data['action']='UpdateSort';
    data['changed_id']=changed_id;
    SendAjaxRequest(
        {
            'data':data
        }
    );

}


function SavePhotosSort(list,name) {
    var data={};
    data['name']=name;
    data['data_sort']=list;
    data['item_id']=$('[name="item_id"]').val();
    SendAjaxRequest(
        {
            'url':'/manager/files/sort_media/',
            'data': data
        }
    );
}

function SaveDocsSort(list) {
    var data={};
    data['action']='sort_docs';
    data['data_sort']=list;
    data['item_id']=$('[name="item_id"]').val();
    SendAjaxRequest(
        {
            'data': data
        }
    );
}

function UpdateProductStatusIcon(response,ajax_config,textStatus,jqXHR) {
    if (ajax_config.data.value==false) {
        ajax_config.options.obj.removeClass('yes').addClass('no');
    }
    else {
        ajax_config.options.obj.removeClass('no').addClass('yes');
    }
}
function ChangeBadgeStatus(obj) {
    var data={};
    data['action']='update_badge_status';
    data['object_id']=obj.attr('data-value');
    data['type_id']=obj.attr('data-type');
    var val=false;
    if (obj.hasClass('no')) {
        val=true;
    }
    data['value']=val;


    var options={};
    options['obj']= obj;
    options['AfterDone']= UpdateProductStatusIcon;
    SendAjaxRequest(
        {
            'data': data,
            'options':options
        }
    );
}
function ChangeObjectStatus(obj) {
    var data={};
    data['action']='update_status';
    data['object_id']=obj.attr('data-value');
    var val=false;
    if (obj.hasClass('no')) {
        val=true;
    }
    data['value']=val;


    var options={};
    options['obj']= obj;
    options['AfterDone']= UpdateProductStatusIcon;
    SendAjaxRequest(
        {
            'data': data,
            'options':options
        }
    );
}
function ChangeProductGab(obj) {
    var data={};
    data['action']='update_gab';
    data['item_id']=obj.attr('data-item');
    data['weight']=$('.product_gab_action[data-item="'+data['item_id']+'"][data-gab="weight"]').val();
    data['length']=$('.product_gab_action[data-item="'+data['item_id']+'"][data-gab="length"]').val();
    data['width']=$('.product_gab_action[data-item="'+data['item_id']+'"][data-gab="width"]').val();
    data['height']=$('.product_gab_action[data-item="'+data['item_id']+'"][data-gab="height"]').val();

    SendAjaxRequest(
        {
            'data': data
        }
    );
}
function ChangeProductAmount(obj) {
    var data={};
    data['action']='update_amount';
    data['item_id']=obj.attr('data-value');
    data['value']=obj.val();

    SendAjaxRequest(
        {
            'data': data
        }
    );
}

function ChangeOrderColor(response,ajax_config,textStatus,jqXHR) {
  ajax_config['options']['obj'].closest('.value').find('div').css('background-color',ajax_config['options']['obj'].css('background-color'));
    ajax_config['options']['obj'].closest('.value').find('div span').html(ajax_config['options']['obj'].html());
}
function ChangeOrderStatus (obj) {
    var data={};
    data['action']='update_status';
    data['status_id']=obj.attr('data-id');
    data['value']=obj.val();

    var options={};
    options['obj']= obj;
    options['AfterDone']=ChangeOrderColor;
    SendAjaxRequest(
        {
            'data': data,
            'options':options
        }
    );
}
function ChangeOrderItemStatus (obj) {
  var data={};
  data['action']='update_status2';
  data['status_id']=obj.attr('data-id');
  data['oid']=obj.attr('data-oid');

  var options={};
  options['obj']= obj;
  options['AfterDone']=ChangeOrderColor;
  SendAjaxRequest(
    {
      'data': data,
      'options':options
    }
  );
}

function GetSubCatSelect(obj) {

        if (obj.val()>0) {
            var data={};
            data['action']='get_sub_select';
            data['parent_id']=obj.val();
            var options={};
            options['insert_elem']=obj.parent();
            SendAjaxRequest(
                {
                    'url':'/manager/shop/cats/',
                    'data': data,
                    'options':options
                }
            );
        }
}

function GetSectionGroups (obj) {
    var data={};
    data['action']='get_section_groups';
    data['section_id']=obj.val();
    var options={};
    options['insert_elem']=$('[data-name="service_group_id"]');
    SendAjaxRequest(
        {
            'url':'/manager/clinic/services/sections/',
            'data': data,
            'options':options
        }
    );
}



function DeleteWorkerServiceDone(response,ajax_config,textStatus,jqXHR) {
    ajax_config['options']['obj'].closest('li').remove();
}

function SaveCatsFiltersOrder (list) {
    var data={};
    data['action']='sort';
    data['data_sort']=list;
    data['cat_id']=$('[name="cat_id"]').val();
    SendAjaxRequest(
        {
            'data': data
        }
    );

}
function MassPriceDone(response,ajax_config,textStatus,jqXHR) {
  if (response.status) {
    FilterTableData(1,ajax_config.options.obj);
  }
}
