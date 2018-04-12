jQuery(document).ready(function($) {
    if (typeof wp.media === 'undefined') return;

    var box = $('#knife-story-box');

    // sort items
    box.sortable({
        items: '.item',
        handle: '.item__field-drag',
        placeholder: 'dump'
    }).disableSelection();


    // clear item
    var clear = function(item) {
        $.each(['color', 'image', 'text'], function(i, cl) {
            item.find('.item__' + cl).val('');
        });

        item.find('.item__display-image').remove();

        return item;
    }

    var dimmer = function(element, cl) {
        element.addClass(cl).delay(500).queue(function(){
            element.removeClass(cl).dequeue();
        });
    }


    // display image
    var display = function(item, link) {
        var parent = item.find('.item__display');

        // set image url to hidden input
        item.find('.item__image').val(link);

        // change src if image already exists
        if(parent.find('.item__display-image').length > 0)
            return parent.find('.item__display-image').attr('src', link);

        // otherwise create new image
        var image = $('<img />', {class: 'item__display-image', src: link});

        return image.prependTo(parent);
    }


    // add new item
    box.on('click', '.actions__add', function(e) {
        e.preventDefault();

        var last = box.find('.item').last(),
            copy  = clear(last.clone());

        return last.after(copy);
    });


    // delete or clear item
    box.on('click', '.item__field-trash', function(e) {
        e.preventDefault();

        var item = $(this).closest('.item');

        if(box.find('.item').length === 1)
            return clear(item);

        return item.remove();
    });


    // change item text color
    box.on('click', '.item__field-color', function(e) {
        e.preventDefault();

        var color = $(this).closest('.item').find('.item__color');

        if(!color.val())
            return color.val(1);

        return color.val('');
    });


    // clone image
    box.on('click', '.item__field-clone', function(e) {
        e.preventDefault();

        var first = box.find('.item').first(),
            link  = first.find('.item__image').val(),
            item  = $(this).closest('.item');

        if(link.length < 1)
            return dimmer(first.find('.item__display'), 'item__display--error');

        item.find('.item__image').val(link);

        return display(item, link);
    });


    // add item image
    box.on('click', '.item__display', function(e) {
        e.preventDefault();

        var item = $(this).closest('.item');

        // open default wp.media image frame
        var frame = wp.media({
            title: knife_story_manager.choose,
            multiple: false
        });

        // on image select
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();

            return display(item, attachment.url);
        });

        return frame.open();
    });

});
