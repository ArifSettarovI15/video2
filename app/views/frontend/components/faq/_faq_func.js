$(document).on('click', '.faqs_item_control', function(){
    if ($(this).hasClass('faqs_item_control_active')) {
        $(this).removeClass('faqs_item_control_active')
        $(this).closest('.faqs_item').find('.faqs_item_content').removeClass('faqs_item_content_active')
    }else{
        $(this).addClass('faqs_item_control_active')
        $(this).closest('.faqs_item').find('.faqs_item_content').addClass('faqs_item_content_active')
    }
})