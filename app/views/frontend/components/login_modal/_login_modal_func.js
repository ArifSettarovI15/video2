$(document).on('click', '.login_modal_open', function(){
    openInlineModal("#modal_login")
    console.log(123)
})
function LoginDone(response){
    console.log(123)
    if (response.status){
        if (response.payment){
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
        }
    }
}