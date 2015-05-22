jQuery(function ($) {
    function bindRemove(){
        $('button[data-collection="remove"]').unbind('click').on('click',function () {
            $(this).parent().parent().parent().remove();
            return false;
        });
    }
    var prototype = $('#sln-availabilities div[data-collection="prototype"]');
    var html = prototype.html();
    var count = prototype.data('count');
    prototype.remove();
    bindRemove();

    $('button[data-collection="addnew"]').click(function () {
        $('#sln-availabilities div.items').append('<div class="item">'+html.replace(/__new__/g, count)+'</div>');
        count++;
        bindRemove();
        return false;
    });
    $('#booking-accept, #booking-refuse').click(function(){
       $('#post_status').val($(this).data('status')); 
       $('#save-post').click();
    });
});
