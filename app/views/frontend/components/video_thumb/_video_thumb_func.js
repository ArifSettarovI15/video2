$(document).on('click','.js_select_video', function (){
    var checkbox = $(this).find('.js_select_video_checkbox')

    if (checkbox.is(':checked')){
        checkbox.prop('checked', false);
    }else{
        checkbox.prop('checked', true);
    }
})

$(document).on('click','.js_select_video', function (){
    var checkbox = $(document).find('.js_select_video_checkbox')

    var titles = []
    var price = 0
    var price_for_sale = 0
    var price_without_sale = 0
    var count = 0
    var count_for_sale =0
    checkbox.each(function (i,item){
        if ($(item).is(':checked')){
            titles.push($(item).attr('data-video_title'))
            price+=parseInt($(item).attr('data-video_price'))
            if (parseInt($(item).attr('data-video_sale'))){
                price_for_sale+=parseInt($(item).attr('data-video_price'))
            }else{
                price_without_sale+=parseInt($(item).attr('data-video_price'))
            }
            count++
            count_for_sale++
        }
    })
    if (count_for_sale < 11) {
        $('span.selected_videos_price').html(price)
    }
    else if(count_for_sale >= 11 && count_for_sale < 21){
        $('span.selected_videos_price').html(parseInt(price_without_sale + (price_for_sale/100*93)))
    }
    else if(count_for_sale >= 21 && count_for_sale < 31){
        $('span.selected_videos_price').html(parseInt(price_without_sale + (price_for_sale/100*86)))
    }
    else if(count_for_sale >= 31 && count_for_sale < 41){
        $('span.selected_videos_price').html(parseInt(price_without_sale + (price_for_sale/100*79)))
    }
    else if(count_for_sale >= 41 && count_for_sale < 51){
        $('span.selected_videos_price').html(parseInt(price_without_sale + (price_for_sale/100*72)))
    }else{
        $('span.selected_videos_price').html(parseInt(price_without_sale + (price_for_sale/100*65)))
    }
    $('span.selected_videos_count').html(count)
    $('ul.cart_info_videos').html('')
    for (var i = 0; i < titles.length; i++) {
        $('ul.cart_info_videos').append('<li><span class="cart_info_videos_line"></span>' + titles[i] + '</li>')
    }
    if (count > 0) {
        $('.cart_info_button_videos').removeClass('cart_info_button_disabled')
        $('.cart_info_button_videos').addClass('js_buy_videos')
    }else{
        $('.cart_info_button_videos').addClass('cart_info_button_disabled')
        $('.cart_info_button_videos').removeClass('js_buy_videos')
    }
})

$(document).on('click', '.js_buy_videos', function(){

    var checkbox = $(document).find('.js_select_video_checkbox')
    var ids=[]

    checkbox.each(function (i,item){
        if ($(item).is(':checked')){
            ids.push(parseInt($(item).attr('data-video_id')))
        }
    })
    SendAjaxRequest({
        data: {'action':'buy_videos',ids:ids},
        onComplete: BuyVideosComplete
    })
})

function BuyVideosComplete(response){
    if (response.status){
        if (response.login){
            openInlineModal(response.html)
        }else{
            if (response.data.checkout_url){
                window.location = response.data.checkout_url
            }
        }
    }
}