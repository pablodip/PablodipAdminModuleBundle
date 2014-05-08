$(function () {

    $('body').on('click', '[data-method]', {}, function (event) {
        event.preventDefault();

        var result = true
        if ($(this).attr("data-are-you-sure") == 1) {
            result = confirm("Are you sure?");
        }
        if (result) {
            $('<form>')
                .attr({method: 'POST', action: $(this).attr('href')})
                .hide()
                .append($('<input>').attr({type: 'hidden', name: '_method', value: $(this).attr('data-method')}))
                .appendTo('body')
                .submit()
            ;
        }
    });

    $('#admin-list-advanced-search-switch').click(function () {
        $('#admin-list-advanced-search').slideToggle();

        return false;
    });

    $('#admin-list-advanced-search-cancel').click(function () {
        $('#admin-list-advanced-search').slideToggle();

        return false;
    });

    $('.admin-list-th-checkbox input').click(function () {
        var inputs = $('.admin-list-table .admin-list-td-checkbox input');
        if ($(this).attr('checked')) {
            inputs.attr('checked', 'checked');
        } else {
            inputs.removeAttr('checked');
        }
    });

    $('#admin-batch-form').submit(function () {
        var elements = [];

        // look for elements only if all is not selected
        if (!$('#admin-batch-form input[name="all"]').attr('checked')) {
            $('.admin-list-td-checkbox input').each(function (index, value) {
                var element = $(value);
                if (element.attr('checked')) {
                    elements[elements.length] = element.attr('value');
                }
            });

            $('#admin-batch-form input[name="ids"]').attr('value', elements.join(','));
        }
    });
});
