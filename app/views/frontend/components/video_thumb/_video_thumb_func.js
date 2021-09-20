$(document).on('click','.js_select_video', function (){
    var checkbox = $(this).find('.js_select_video_checkbox')
    console.log($(this))
    if (checkbox.is(':checked')){
        checkbox.prop('checked', false);
    }else{
        checkbox.prop('checked', true);
    }
})