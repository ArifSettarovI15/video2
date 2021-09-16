function TabsSet(item) {
    var tab_block=item.block();
    var tabs_block_id=tab_block.attr('data-tab');

    var tab_id=item.attr('data-id');
    var tabs_content=$('.tab-content[data-tab="'+tabs_block_id+'"]');

    tab_block.elem('link').delMod('active');
    item.mod('active', true);

    tabs_content.elem('content').delMod('active').removeClass('fadeIn').addClass('fadeOut');
    tabs_content.elem('content').filter( '[data-id="'+tab_id+'"]' ).mod('active',true).removeClass('fadeOut').addClass('fadeIn');

    if (tab_block.attr('data-after')) {
        window[tab_block.attr('data-after')]()
    }
}


function openTab(tab,item_id) {
  var tabs_block_id=tab;
  var tab_block=$('.tabs[data-tab="'+tab+'"]');

  var tab_id=item_id;
  var tabs_content=$('.tabs-content[data-tab="'+tabs_block_id+'"]');

  tab_block.elem('item').delMod('active');
  tab_block.find('[data-id="'+item_id+'"].tabs__item').mod('active',true);

  tabs_content.elem('content').delMod('active');
  tabs_content.elem('content').filter( '[data-id="'+tab_id+'"]' ).mod('active',true);
}

function initSliderTabs() {
    var container = $('.tabs__inner')
    if (container.length > 0) {
        var options = {
            horizontal: 1,
            itemNav: 'basic',
            smart: 1,
            activateOn: 'click',
            mouseDragging: 1,
            touchDragging: 1,
            releaseSwing: 1,
            startAt: 0,
            scrollBy: 1,
            activatePageOn: 'click',
            speed: 300,
            elasticBounds: 1,
            dragHandle: 1,
            dynamicHandle: 1,
            clickBar: 1,
            slidee: container.find('.tabs__list'),
            itemSelector: container.find('.tabs__item').eq(0),
        };

        container.each(function(i, elem) {
            new Sly(elem, options).init()
        })
    }
}

initSliderTabs()
