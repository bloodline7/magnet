import $ from "jquery";
require ("./jquery.multi-select");

$(document).on('load', function (event, target) {

    $(target.target).find("select[multiple]").multiSelect();
});

