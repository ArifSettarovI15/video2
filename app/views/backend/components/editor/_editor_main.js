if ($('.rich').length>0) {
    tinymce.baseURL = "/assets/vendors/tinymce";
    tinymce.init({
        selector: ".rich",
        language: "ru",
        file_browser_callback_types: 'image',
        file_picker_types: 'image',
        images_upload_credentials: true,
        automatic_uploads: true,
        relative_urls : false,
        remove_script_host : true,
        convert_urls : true,

        setup: function (editor) {
            editor.on('init', function(args) {
                editor = args.target;

                editor.on('NodeChange', function(e) {
                    if (e && e.element.nodeName.toLowerCase() == 'img') {
                        width = e.element.width;
                        height = e.element.height;
                        tinyMCE.DOM.setAttribs(e.element, {'width': null, 'height': null});
                        tinyMCE.DOM.setAttribs(e.element,
                            {'style': 'max-width:100%;'});
                    }
                });
            });
        },
        file_browser_callback: function(field_name, url, type, win) {
            var filebrowser = "/files/list/?module=content&folder_name=content&input_name=content_file";
            tinymce.activeEditor.windowManager.open({
                title : "Менеджер файлов",
                width : 800,
                height : 500,
                url : filebrowser
            }, {
                window : win,
                input : field_name
            });
            return false;
        },
      fontsize_formats: '12px 14px 16px 18px 20px 22px 24px',
      height: 500,
      theme: 'modern',
      plugins: [
        "advlist  autolink link image lists charmap print preview hr anchor pagebreak spellchecker emoticons",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save table contextmenu directionality emoticons template paste colorpicker textcolor "
      ],
      image_advtab: true,
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect fontsizeselect emoticons",
        toolbar2: " | link unlink anchor | image media | forecolor backcolor  | print preview code ",
    });
}


$( document ).on( "click", ".emoji-widget__title", function() {
  $('.emoji-widget__title').mod('active', false);
  $(this).mod('active', true);

  $('.emoji-widget__item').mod('active', false);
  $('.emoji-widget__item_'+$(this).attr('data-id')).mod('active', true);
});
