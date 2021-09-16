function CloseModals() {
    var magnificPopup = $.magnificPopup.instance;
    magnificPopup.close();
}

function onOpenModal() {
    initForms()
}

function openInlineModal(src, trigger) {
    var aOpen;
    var aClose;
    if (trigger) {
        aOpen = trigger.attr('data-effect');
        aClose = trigger.attr('data-effect-close');
    }
    $.magnificPopup.open({
        fixedContentPos: true,
        fixedBgPos: true,
        closeOnBgClick: true,
        overflowY: 'auto',
        showCloseBtn: false,
        preloader: false,
        midClick: true,
        items: {
            src: src,
            type: 'inline'
        },
        callbacks: {
            open: function () {
                onOpenModal();
                if (trigger && trigger.attr('data-after')) {
                    var func = window[trigger.attr('data-after')]
                    if (func) {
                        func()
                    }
                }
            },
            close: function () {
                CloseModals();
            },
            beforeOpen: function () {
                if (trigger && trigger.attr('data-before')) {
                    var func = window[trigger.attr('data-before')]
                    if (func) {
                        func(this, trigger)
                    }
                }

                initScroll($('.modal-search__list'))

                var cl = '';
                if (aOpen) {
                    cl = aOpen;
                } else {
                    cl = '';
                }
                // $('.modal').removeClass(aClose).addClass(cl + ' animated ');
            },
            beforeClose: function () {
                var cl = '';
                if (aClose) {
                    cl = aClose;
                } else {
                    cl = '';
                }
                // $('.modal').removeClass(aOpen + ' animated ').addClass(cl + ' animated ');
            },
            afterClose: function () {
                var cl = ''
                if (aClose) {
                    cl = aClose;
                } else {
                    cl = '';
                }

                // $('.modal').removeClass(cl)
            }
        }
    });
    grunticon.embedIcons(grunticon.getIcons(grunticon.href));
}

function afterCloseMainModal() {
    $($.magnificPopup.instance.st.items.src).removeClass('mfp-hide')
}

// function initModal() {
//   $('.popup-modal').magnificPopup({
//     type: 'inline',
//     preloader: false,
//     fixedContentPos: true,
//     showCloseBtn: false,
//     fixedBgPos: true,
//     removalDelay: 300,
//     overflowY: 'scroll',
//     callbacks: {
//       open: function() {
//         onOpenModal();
//       },
//
//       beforeOpen: function() {
//         clearSelects();
//         var cl = '';
//         if (this.st.el.attr('data-effect')) {
//           cl = this.st.el.attr('data-effect');
//         } else {
//           cl = 'zoomInUp';
//         }
//         $('.modal').addClass(cl + ' animated ');
//       },
//
//       close: function() {
//         CloseModals();
//       },
//
//       afterClose: function() {
//         afterCloseModals();
//       },
//
//       beforeClose: function() {
//         var cl = '';
//         if (this.st.el.attr('data-effect')) {
//           cl = this.st.el.attr('data-effect');
//         } else {
//           cl = 'zoomInUp';
//         }
//         $('.modal').removeClass(cl + ' animated fast ').addClass('fadeOut animated ');
//       }
//     }
//   });
// }
