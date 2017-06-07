$(document).ready(function () {
    pagination(paginationCustom);


    $('a[id^="upcoming-events-"]').click(function () {
        monthId     = $(this).attr('id').replace('upcoming-events-', '');
        month       = $(this).attr('data-month');
        year        = $(this).attr('data-year');
        eventType   = $(this).attr('data-month-type') + ' events';
        archive   = $(this).attr('data-archive');
        $.ajax({
            url: eventURL,
            type: "POST",
            data: {'month': monthId, 'year': year, 'archive': archive},
            headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
            dataType: 'json',
            success: function (data) {
                $('#event-content').html(data.html_content);
                $('#selected-month').html(month);
                $('#event-type').html(eventType);
                $("#selected-month").attr("data-month-id", monthId);
                $("#selected-month").attr("data-year-id", year);
                pagination(paginationCustom);
                $('#event-content .three-column-group .group-item .group-banner').each(function(){
                    $(this).after('<div class="group-banner-bg" style="background-image:url('+$(this).attr('src')+');"></div>');
                });
            },
            error: function (e) {
                console.log('error', e);
                alert("Some error. Please try refreshing page.");
            }
        });
    });
});

var pagination = function (changePath) {
    $('.pagination').hide();
    var loading_options = {
        finishedMsg: "",
        msgText: "",
        msg : '',
        img: loadingImageURL,
    };
    $('#blog-type-data').infinitescroll({
        loading: loading_options,
        navSelector: "#infinite-scroll .pagination",
        nextSelector: "#infinite-scroll .pagination li.active + li a",
        itemSelector: "#blog-type-data li.item",
        path: function (index) {
            if (changePath == '1') {
                return "?page=" + index + "&month=" +
                        $("#selected-month").attr("data-month-id") + "&year=" + $("#selected-month").attr("data-year-id");
            } else if (changePath == 'keyword') {
                return "?page=" + index + "&keyword=" +
                        $("#contentKeyword").val() + "&content=" + $("#contentId").val()+ "&relative=FALSE&selective=TRUE";
            }
            else {
                return "?page=" + index
            }
        }
    });
};