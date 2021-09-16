$(function() {

    $(document).click(function(event) {
        if(!$(event.target).closest('.search_result').length
        ) {
            if($('.search_result').is(":visible")) {
                $('.search_result').slideUp();
            }
        }
    });


    /*
     Home page
     */

    // Filter tabs
    $( document ).on( "click", ".tabs_switcher", function(e) {
        e.preventDefault();
        SwitchTab($(this));
    });


    $( document ).on( "click", ".cat_content .paging_block a", function(e) {
        e.preventDefault();
        var obj=$(this);
        FilterCatsItems(obj.attr('data-value'),obj,'paging');
    });
    $( document ).on( "click", ".news_list_page .paging_block .page", function(e) {
        e.preventDefault();
        var obj=$(this);
        PagingNews(obj.attr('data-value'),obj);
    });


    $( document ).on( "change", ".cat_filters [data-value='filter_value']", function() {
        FilterCatsItems(1,$(this),'filter');
    });
    $( document ).on( "click", ".m_top.filters .button_1", function() {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        }
        else {
            $(this).addClass('active');
        }
        FilterCatsItems(1,$(this));
    });


    /*
    Cats
     */
    $('.cats_select_filter').on('change', function(){
        if ($(this).val()!=''){
            window.location.href=$(this).val();
        }
    });




    $( document ).on( "click", ".orders_page  .order", function(e) {
        SelectOrder($(this).attr('data-id'));
    });

    $( document ).on( "click", ".orders_page   .cancel_order_action", function(e) {
       CancelOrder($(this));
    });




    $( document ).on( "change", ".slider_filter", function(e) {
        FilterSlides($(this));
    });


        var mobileSideMenu = ({

            init: function() {
                this.$menuEl = $('.nav_mobile');
                this.$triggerEl = $('header .mobile_menu_link i');
                this.$layout = $('.wrapper');
                this.bind();
            },

            bind: function(){



                var _this = this;


                this.$triggerEl.click(function(){
                    _this.$menuEl.toggleClass('nav_mobile_open');
                    if (_this.$layout.hasClass('content_nav-mobile-open')) {
                        setTimeout(function() { $('.nav_top_bottom').hide() }, 500)
                        $('body').removeClass('menu_opened');
                        _this.$layout.addClass('content_nav-mobile-close').removeClass('content_nav-mobile-open');
                    } else {
                        $('.nav_top_bottom').show();
                        $('body').addClass('menu_opened');
                        $("html, body").scrollTop(0);
                        _this.$layout.addClass('content_nav-mobile-open').removeClass('content_nav-mobile-close');
                    }


                    return false
                });

            }

        }).init();


});

