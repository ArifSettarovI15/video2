$( document ).on( "focus click", ".element.input-style_error", function() {
    ClearElement($(this));
});
$( document ).on( "click", ".form .submit:not(.disabled)", function() {
    var form_obj=$(this).closest('.form');
    AddObject(form_obj);
});

$('.form input.element[type="text"],.form input.element[type="password"],.form input.element[type="email"]').keyup(function(e){
    if(e.keyCode == 13)
    {
        var form_obj=$(this).closest('.form');
        AddObject(form_obj);
    }
});
$('form.form').submit( function(e){
    e.preventDefault();
});

$( document ).on( "click focus", ".input__input", function(e) {
    if ($(this).hasMod('disabled')) {

    }
    else {
        // cleanActiveInputs();
        $(this).block().mod('active', true);
    }
});

initForms();

$(document).on('input', '.field__input', function(e) {
    var $close = $(this).siblings('.field__clean')

    if ($(this).val().length > 0) {
        $(this).addClass('focused');
        if ($(this).val().length > 2) {
            $close.show()
        }
    } else {
        $close.hide()
        $(this).removeClass('focused');
    }
})

$(document).on('click tap', '.js-input-clean', function(e) {
    var input = $(this).siblings('input')
    input.val('').removeClass('focused')
    $(this).hide()
    $(this).block().mod('opened', false)
})

$('.js-acc-head').each(function(i, elem) {
    var hammerTime = new Hammer(elem, {
        domEvents: true
    })

    hammerTime.on('tap', function() {
        slideFooterMenu($(elem))
    })
})

function slideFooterMenu(trigger) {
    var parent = trigger.closest('.js-acc-parent')
    parent.siblings().mod('active', false);
    if (parent.hasMod('active')) {
        parent.mod('active', false);
    } else {
        parent.mod('active', true);
    }
}

initScroll($('.js-scroll'))

$(document).on('keyup', '.field_search .field__input', function (e) {
    if ($(this).val() !== '') {
        $(this).block().mod('opened', true);
    } else {
        $(this).block().mod('opened', false)
    }
    initScroll($(this).block().find('.field-list'))
    // searchTop($(this).block());

});

$(document).on('click', '.js-search-select', function() {
    var val = $(this).html().replace(/<\/?[a-zA-Z]+>/gi,'').trim();
    $(this).closest('.field').elem('input').attr('data-id',$(this).attr('data-id')).val(val);
    closeSeachDop();
})

$(document).on('click', function(e) {
    var a = $('.field_search.field_opened');

    a.is(e.target) || a.has(e.target).length !== 0 || closeSeachDop();
})

$('.textfield__field').textareaAutoSize()

function closeSeachDop() {
    $('.field_search').mod('opened', false);
    $('.js-search-block__form-list').mod('hide', true);
}


