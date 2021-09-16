function destroySlide(slider) {
    slider.each(function (a, b) {
        $(this).slick('unslick');
    });
};

function slideLavaDots(slider) {
    var lavalampOptions = {
        autoUpdate: true,
        easing: 'easeInOutCubic'
    }
    var lava = false
    return function() {
        var $dots = $('.slick-dots')

        slider.on('init', function(event, slick) {
            $dots = slick.$dots
            if ($dots) {
                $dots.each(function(i, elem) {
                    if (!$(elem).hasClass('lavalamp')) {
                        $(elem).lavalamp(lavalampOptions)
                    }
                })
                lava = true

            }

            $(window).on('orientationchange resize', function(event) {
                if (lava) {
                    $dots.each(function(i, elem) {
                        if ($(elem).hasClass('lavalamp')) {
                            $(elem).lavalamp('destroy')
                        }
                    })
                    lava = false
                }
            })
        })

        // slider.on('breakpoint', function(event, slick, breakpoint) {
        //     $dots = slick.$dots
        //     if ($dots.hasClass('lavalamp')) {
        //         $dots.lavalamp('update')
        //     }
        // })

        slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            $dots = slick.$dots
            if ($dots.hasClass('lavalamp')) {
                var a =  $dots.children().eq(nextSlide)
                $dots.data('lavalampActive', a).lavalamp('update');
            }
        })
    }
}

