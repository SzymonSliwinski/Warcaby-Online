"use strict";

$("img").click(function (e) {
    e.preventDefault();
    var f = $(this).attr('name');

    $.ajax({
        url: '/controller.php?set_cookie=true&value=' + f,
        type: 'get',
        success: function (data) {
            location.reload();
        }
    });
});

$(".font-changer").click(function (e) {
    e.preventDefault();
    var f = $(this).attr('name');
    $.ajax({
        url: '/controller.php?set_cookie_font=true&value=' + f,
        type: 'get',
        success: function (data) {
            location.reload();
        }
    });
});

$(".theme-changer").click(function (e) {
    e.preventDefault();
    var f = $(this).attr('name');
    $.ajax({
        url: '/controller.php?set_cookie_theme=true&value=' + f,
        type: 'get',
        success: function (data) {
            location.reload();
        }
    });
});
