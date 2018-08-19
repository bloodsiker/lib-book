$(function() {
    $('.more-btn').on('click', function (event) {
        var target = $(event.target),
            url = target.attr('data-url') + '?page=' + target.attr('data-page');

        if (target.attr('data-category') !== undefined) {
            url += '&category=' + target.attr('data-category');
        }

        if (target.attr('data-id') !== undefined) {
            url += '&id=' + target.attr('data-id');
        }

        if (target.attr('data-slug') !== undefined) {
            url += '&slug=' + target.attr('data-slug');
        }

        if (target.attr('data-type') !== undefined) {
            url += '&type=' + target.attr('data-type');
        }

        if (target.attr('data-keyword') !== undefined) {
            url += '&keyword=' + target.attr('data-keyword');
        }

        $.ajax(url, {}).done(function (data) {
            target.closest('section').find('div.row').append(data);
            if (typeof $(target).attr('data-limit') !== 'undefined' && typeof $(target).attr('data-big-image') !== 'undefined') {
                if (parseInt(target.attr('data-page')) * parseInt(target.attr('data-limit')) + parseInt(target.attr('data-big-image')) > target.closest('section').find('.multipage-item').length) {
                    target.parent().remove();
                }
            }
            target.attr('data-page', parseInt(target.attr('data-page'), 10) + 1);
        });
    });
});
