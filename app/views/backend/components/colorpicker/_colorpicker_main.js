$(".sprav_colors [name='svid_value']").colorpicker({
    regional:       'ru',
    colorFormat: '#HEX',
    parts:	['map', 'bar'],
    layout: {
        map:		[0, 0, 1, 1],	// Left, Top, Width, Height (in table cells).
        bar:		[1, 0, 1, 1]
    },
    part:	{
        map:		{ size: 128 },
        bar:		{ size: 128 }
    }

});

$( document ).on( "change", ".sprav_colors [name='svid_value']", function() {
    $('.colorpicker-box').css('background-color',$(this).val());
});
