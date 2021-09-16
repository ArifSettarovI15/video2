

/*
General
 */


/*
 Tabs
 */
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


/*
Modal form
 */



function GetFilterValue(data_array,obj) {
    var val;
    if (obj.attr('data-group')) {
        if (obj.hasClass('active')) {
            val =1;
        }
        else {
            val=0;
        }
        if (data_array[obj.attr('data-group')]==undefined) {
            data_array[obj.attr('data-group')]={};
        }
        data_array[obj.attr('data-group')][obj.attr('data-name')]=val;
    }
    else {
        if (obj.attr('data-option-value')) {
            if (obj.hasClass('active')) {
                data_array[obj.attr('data-name')] = obj.attr('data-option-value');
            }
        }
        else {
            data_array[obj.attr('data-name')] = obj.val();
        }
    }
    return data_array;
}

/*
GG
 */




function goBack() {
    window.history.back();
}
function PagingNews(page,obj) {
    var data_array={};
    if (page % 1 === 0) {

    }
    else {
        page=1;
    }
    data_array['page']=page;
    $('.cat_filters').find('[data-value="filter_value"]').each(function(index,value) {
        data_array=GetFilterValue(data_array,$(this));
    });
    var options={};
    options['obj']=obj.closest('.wrapper');
    options['AfterDone'] = PagingNewsDone;
    SendAjaxRequest(
        {
            'data':data_array,
            'options':options,
            'onlyOne':true
        }
    );
}
function FilterCatsItems(page,obj,action) {
    var data_array={};
    if (page % 1 === 0) {

    }
    else {
        page=1;
    }
    data_array['page']=page;
    $('.cat_filters').find('[data-value="filter_value"]').each(function(index,value) {
        data_array=GetFilterValue(data_array,$(this));
    });
    var options={};
    options['obj']=obj.closest('.wrapper');
    if (action=='paging') {
        options['AfterDone'] = FilterCatsItemsDone2;
    }
    else {
        options['AfterDone'] = FilterCatsItemsDone;
    }
    SendAjaxRequest(
        {
            'data':data_array,
            'options':options,
            'onlyOne':true
        }
    );
}
function PagingNewsDone(response,ajax_config,textStatus,jqXHR) {
    ajax_config['options']['obj'].find('.block_news .list').html(response.html)
    ajax_config['options']['obj'].find('.paging_block').html(response.paging);
    ajax_config['options']['obj'].find('.items_total').html(response.total);
    $("html, body").animate({scrollTop: $('.cat_filters').offset().top-$('.top_line').height()}, 750);
}
function FilterCatsItemsDone(response,ajax_config,textStatus,jqXHR) {
    ajax_config['options']['obj'].find('.cat_items').html(response.html)
    ajax_config['options']['obj'].find('.paging_block').html(response.paging);
    ajax_config['options']['obj'].find('.items_total').html(response.total);
    $("html, body").animate({scrollTop: $('.cat_filters').offset().top-$('.top_line').height()}, 750);
}
function FilterCatsItemsDone2(response,ajax_config,textStatus,jqXHR) {
    ajax_config['options']['obj'].find('.cat_items').append( response.html );
    ajax_config['options']['obj'].find('.paging_block').html(response.paging);
    $("html, body").animate({scrollTop: $('.cat_items .products_list:last-child').offset().top-$('.top_line').height()}, 750);
}





function SearchDone(response,ajax_config,textStatus,jqXHR) {
   if (response.html!='') {
        $('.search_result').html(response.html);
       $('.search_result').slideDown();
   }
    else {
       $('.search_result').slideUp();
   }
}
var sliders={};
function FilterSlides(obj) {
    var slider_name=obj.closest('.products_list').attr('data-name');
    sliders[slider_name].destroySlider();
    obj.closest('.products_list').find('.bxslider').html('');
    var val=obj.val();
    if (val!=0) {
        $(obj.closest('.products_list ').find('.slider_temp li[data-filter="' + val + '"]')).each(function (index, value) {
            obj.closest('.products_list').find('.bxslider').append($(this).clone());
        });
    }
    else {
        obj.closest('.products_list').find('.bxslider').html(obj.closest('.products_list ').find('.slider_temp').html());
    }
    sliders[slider_name]=MakeProductsSlider(slider_name);
}

function OrderDone(response,ajax_config,textStatus,jqXHR) {
    if (response.status) {
        $('h1').html(response.h1);
        $('.sub_title').html(response.sub_title);
        var head='<div class="title_1"><div class="title">Ваш заказ <span>#'+response.order_id+'</span></div></div>';
        $('.title_1').remove();
        $( ".basket_page .container" ).html( response.html );
        $('.process_order ').remove();
        $('.under_table').remove();
        $("html, body").animate({scrollTop: $('.page_header').offset().top-$('.top_line').height()}, 750);
    }
}

