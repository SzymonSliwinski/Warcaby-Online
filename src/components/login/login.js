"use strict";

$.ajax({
    url: '/controller.php?set_cookie_font=true&value=smaller',
    type: 'get',
    success: function (data) { }
});

$.ajax({
    url: '/controller.php?set_cookie_theme=true&value=bright',
    type: 'get',
    success: function (data) {
    }
});

