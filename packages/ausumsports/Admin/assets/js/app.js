console.log('script load');

window.$ = window.jQuery= require ("jquery");

require("bootstrap");


require("./bootstrap-datepicker");
require("./locales/bootstrap-datepicker.en-CA");
require("./ajaxSetup");


require ('./tableSorter');
require ('./tableControl');
require("./contentUpdater");


require("./datePicker");

require("./modalControl");
require("./auth");


require("./laravelEcho");


require("./terminal");

require("./crawling");

require("./multiSelect");
require("./dropzoneControl");
//require("./autoComplete");
//require("./ckeditor5");
//require("./swiper");
require("./imageViewer");

console.log('load all javascript');
