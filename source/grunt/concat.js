module.exports = {
    js_components_frontend_main: {
        options: {
            banner: '$(function() {',
            footer: '});'
        },
        src: [
            '../app/views/frontend/components/**/*_main.js'
        ],
        dest: '../app/views/frontend/components/_components_frontend-main.js'
    },
    js_components_frontend_func: {
        src: [
            '../app/views/frontend/components/**/*_func.js'
        ],
        dest: '../app/views/frontend/components/_components_frontend-func.js'
    },
    js_components_backend_main: {
        options: {
            banner: '$(function() {',
            footer: '});'
        },
        src: [
            '../app/views/backend/components/**/*_main.js'
        ],
        dest: '../app/views/backend/components/_components_backend-main.js'
    },
    js_components_backend_func: {
        src: [
            '../app/views/backend/components/**/*_func.js'
        ],
        dest: '../app/views/backend/components/_components_backend-func.js'
    },
    js_modules_main: {
        options: {
            banner: '$(function() {',
            footer: '});'
        },
        src: [
            '../app/views/modules/**/*_main.js'
        ],
        dest: '../app/views/modules/_modules-main.js'
    },
    js_modules_func: {
        src: [
            '../app/views/modules/**/*_func.js'
        ],
        dest: '../app/views/modules/_modules-func.js'
    },
    js_frontend_build: {
        src: [
            //vendors
            '../assets/images/sprites/grunticon.loader.js',
            'bower_components/jquery/dist/jquery.min.js',
            'bower_components/magnific-popup/dist/jquery.magnific-popup.min.js',
            'bower_components/tooltipster/dist/js/tooltipster.bundle.min.js',
            'bower_components/slick-carousel/slick/slick.min.js',
            'bower_components/js-cookie/src/js.cookie.js',
            'bower_components/select2/dist/js/select2.js',
            'bower_components/jquery.inputmask/dist/jquery.inputmask.bundle.js',
            'bower_components/inputmask-multi/js/jquery.inputmask-multi.min.js',
            'bower_components/hammerjs/hammer.min.js',
            'bower_components/jquery-hammerjs/jquery.hammer.js',
            'bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js',
            'bower_components/nouislider/distribute/nouislider.min.js',
            'bower_components/iCheck/iCheck.min.js',
            'bower_components/vanilla-lazyload/dist/lazyload.js',
            'bower_components/picturefill/dist/pucturefill.js',
            'bower_components/object-fit-images/dist/ofi.browser.js',
            'bower_components/js-cookie/src/js.cookie.js',
            'bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
            'bower_components/blueimp-file-upload/js/jquery.iframe-transport.js',
            'bower_components/blueimp-file-upload/js/jquery.fileupload.js',
            'bower_components/air-datepicker/dist/js/datepicker.js',
            'bower_components/textarea-autosize/dist/jquery.textarea_autosize.min.js',
            'bower_components/lavalamp/js/jquery.lavalamp.js',
            'bower_components/sly/dist/sly.js',
            'bower_components/lazysizes/plugins/object-fit/ls.object-fit.min.js',
            'bower_components/lazysizes/plugins/parent-fit/ls.parent-fit.min.j',
            'bower_components/lazysizes/plugins/respimg/ls.respimg.min.js',
            'bower_components/lazysizes/lazysizes.min.js',
            'src/js/vendors/bem.js',

            //modules
            '../app/views/modules/_modules-main.js',
            '../app/views/modules/_modules-func.js',

            //components
            '../app/views/frontend/components/_components_frontend-main.js',
            '../app/views/frontend/components/_components_frontend-func.js',

            //global
            'src/js/main/global/func.js',
            'src/js/main/global/main.js'
        ],
        dest: 'build/js/frontend.js'
    },
    js_backend: {
        src: [
            //vendors
            'bower_components/jquery/dist/jquery.min.js',
            'bower_components/jquery-ui/jquery-ui.min.js',
            'bower_components/slick-carousel/slick/slick.min.js',
            'bower_components/jquery-ui/ui/i18n/datepicker-ru.js',
            'bower_components/magnific-popup/dist/jquery.magnific-popup.min.js',
            'bower_components/hammerjs/hammer.js',
            'bower_components/jquery-dropdown/jquery.dropdown.min.js',
            'bower_components/js-cookie/src/js.cookie.js',
            'bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js',
            'bower_components/textarea-autosize/dist/jquery.textarea_autosize.min.js',
            'bower_components/tinymce/tinymce.min.js',
            'bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
            'bower_components/blueimp-file-upload/js/jquery.iframe-transport.js',
            'bower_components/blueimp-file-upload/js/jquery.fileupload.js',
            'bower_components/fancybox/source/jquery.fancybox.pack.js',
            'bower_components/datetimepicker/build/jquery.datetimepicker.full.min.js',
            'bower_components/select2/dist/js/select2.full.min.js',
            'bower_components/iCheck/iCheck.min.js',
            'bower_components/jquery.bem/jquery.bem.js',
            'bower_components/colorpicker/jquery.colorpicker.js',
            'bower_components/jstree/dist/jstree.js',

            'src/js/vendors/jquery.mjs.nestedSortable.js',
            'src/js/vendors/jquery.liTranslit.js',
            'src/js/vendors/bem.js',

            //modules
            '../app/views/modules/_modules-main.js',
            '../app/views/modules/_modules-func.js',

            //components
            '../app/views/backend/components/_components_backend-main.js',
            '../app/views/backend/components/_components_backend-func.js',
            '../app/views/frontend/components/_components_frontend-func.js',
            'src/js/main/backend/func.js',
            'src/js/main/backend/main.js',

            //global
            'src/js/main/global/func.js',
            'src/js/main/global/main.js'
        ],
        dest: 'build/js/backend.js'
    }
};
