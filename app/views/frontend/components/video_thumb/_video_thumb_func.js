$(document).on('click','.js_select_video', function (){
    var checkbox = $(this).find('.js_select_video_checkbox')

    if (checkbox.is(':checked')){
        checkbox.prop('checked', false);
    }else{
        checkbox.prop('checked', true);
    }
})

$(document).on('click','.js_select_video', function (){
    createiframe()
    openInlineModal('#modal_tarif')

})

function createiframe(){
    var blobMe= URL['createObjectURL'](new Blob([''], {type: 'text/html'}));
    var elIframe = document['createElement']('iframe');
    elIframe['setAttribute']('frameborder', '0');
    elIframe['setAttribute']('width', '800px');
    elIframe['setAttribute']('height', '450px');
    elIframe['setAttribute']('src', blobMe);
    var idOne= 'gepa_'+ Date.now();
    elIframe['setAttribute']('id', idOne);
    document.getElementById('iframe_here').appendChild(elIframe);
    const iframeHere= 'https://www.youtube.com/embed/00Gxo4zBVhk';
    document.getElementById(idOne)['contentWindow']['document'].write('<script type="text/javascript">location.href = "' + iframeHere + '";</script>')
}

