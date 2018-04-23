jQuery(document).ready(function($) {
    if (typeof wp.media === 'undefined') return;

    var box = $('#knife-story-box');

    // use this variable as storage for current editor id
    box.data('items', 0);


    // sort items
    box.sortable({
        items: '.item',
        handle: '.item__field-drag',
        placeholder: 'dump',
    }).disableSelection();


    // clear item
    var clear = function() {
        var item = box.find('.item').first().clone()

        item.find('.item__text').val('');

        return item;
    }


    // create virtual item element
    var dummy = clear();


    // create wp.editor using item element
    var editor = function(el) {
        var text = el.find('.item__text');
        var edit = text.attr('id');

        if(typeof edit === 'undefined') {
            edit = 'knife-story-text-' + box.data('items');
            text.attr('id', edit);

            box.data().items++;
        }
        else {
            wp.editor.remove(edit);
        }

        wp.editor.initialize(edit, {
            tinymce: true,
            quicktags: true,
            mediaButtons: true
        });
    }


    // add class for short time
    var dimmer = function(element, cl) {
        element.addClass(cl).delay(500).queue(function(){
            element.removeClass(cl).dequeue();
        });
    }


    // display image
    var display = function(parent, link) {
        var image = 'option__background-image';

        // set image url to hidden input
        parent.find('.option__background-input').val(link);

        // change src if image already exists
        if(parent.find('.' + image).length > 0)
            return parent.find('.' + image).attr('src', link);

        // otherwise create new image
        var showcase = $('<img />', {class: image, src: link});

        return showcase.prependTo(parent);
    }


    // set image shadow on load
    var shadow = function(cl) {
        var blank = box.find('.option__background-blank');
        var shade = parseInt(box.find(cl).val()) / 100;

        blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
    }


    // add new item
    box.on('click', '.actions__add', function(e) {
        e.preventDefault();

        var last = box.find('.item').last(),
            copy = dummy.clone();

        last.after(copy);

        return editor(copy);
    });


    // delete or clear item
    box.on('click', '.item__field-trash', function(e) {
        e.preventDefault();

        var item = $(this).closest('.item');

        if(box.find('.item').length === 1)
            box.find('.actions__add').trigger('click');

        return item.remove();
    });


    // add story background
    box.on('click', '.option__background', function(e) {
        e.preventDefault();

        // open default wp.media image frame
        var frame = wp.media({
            title: knife_story_manager.choose,
            multiple: false
        });

        // on image select
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var background = box.find('.option__background');

            display(background, attachment.url);

            // set shadow on image creation
            box.find('.option__range').trigger('change');
        });

        return frame.open();
    });


    // shadow range
    box.on('change', '.option__range', function(e) {
        var blank = box.find('.option__background-blank');
        var shade = parseInt($(this).val()) / 100;

        if(box.find('.option__background-image').length < 1)
            return dimmer(blank, 'option__background-blank--error');

        blank.css('background-color', 'rgba(0, 0, 0, ' + shade + ')');
    });


    // reinit wp editor on drag
    box.on("sortstop", function(event, ui) {
        return editor(ui.item);
    });


    // init wp editors
    box.find('.item').each(function(i, el) {
        return editor($(this));
    });


    return box.find('.option__range').trigger('change');
});
