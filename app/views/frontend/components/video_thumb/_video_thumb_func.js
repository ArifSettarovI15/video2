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
    var price_without_sale = 0
    var price = 0
    var price7 = 0
    var price14 = 0
    var price21 = 0
    var price28 = 0
    var price35 = 0
    var count = 0
    var count_without_sale = 0
    checkbox.each(function (i,item){
        if ($(item).is(':checked')){
            titles.push($(item).attr('data-video_title'))


            if (parseInt($(item).attr('data-video_sale'))){
                ++count
                if (count < 11){
                    price+=parseInt($(item).attr('data-video_price'))
                }
                else if(11 <= count <21 ){
                    price7 +=parseInt($(item).attr('data-video_price'))
                }
                else if(21 <= count <31 ){
                    price14 +=parseInt($(item).attr('data-video_price'))
                }
                else if(31 <= count <41 ){
                    price21 +=parseInt($(item).attr('data-video_price'))
                }
                else if(41 <= count <51 ){
                    price28 +=parseInt($(item).attr('data-video_price'))
                }
                else if(51 <= count ){
                    price35 +=parseInt($(item).attr('data-video_price'))
                }
            }else{
                ++count_without_sale
                price_without_sale+=parseInt($(item).attr('data-video_price'))
            }
        }
    })
    if (price7 > 0) {
        price7 = price7/100*93
    }
    if (price14 > 0) {
        price14 = price14/100*86
    }
    if (price21 > 0) {
        price21 = price21/100*79
    }
    if (price28 > 0) {
        price28 = price28/100*72
    }
    if (price35 > 0) {
        price35 = price35/100*65
    }
    count+=count_without_sale
    price = parseInt(price) + parseInt(price7) + parseInt(price14) + parseInt(price21) + parseInt(price28) + parseInt(price35)+price_without_sale
    $('span.selected_videos_price').html(price)
    $('span.selected_videos_count').html(count)
    $('ul.cart_info_videos').html('')
    for (var i = 0; i < titles.length; i++) {
        $('ul.cart_info_videos').append('<li><span class="cart_info_videos_line"></span>' + titles[i] + '</li>')
    }

    if (parseInt($('ul.cart_info_videos').height()) >=300 ){
        $('ul.cart_info_videos').css({'overflow-y':'scroll'})
    }else{
        $('ul.cart_info_videos').css({'overflow-y':'hidden'})
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

$(document).on('click', '.js_buy_premium', function(){

    SendAjaxRequest({
        data: {'action':'buy_premium'},
        onComplete: BuyPremiumComplete
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
function BuyPremiumComplete(response){
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

$(document).on('click', '.js_watch_video', function (){
    var id = $(this).find('input').attr('data-video_id')


    SendAjaxRequest({data:{'action':'watch_video', 'video_id':id}, onComplete:ShowVideo})
})

function ShowVideo(response, ajax_loading){
    if(response.status) {
        $('body').append(response.html)
        openInlineModal('#modal_video')
    }
}