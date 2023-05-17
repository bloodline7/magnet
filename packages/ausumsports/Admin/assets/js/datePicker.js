import $ from 'jquery';
//const $ = require("jquery-ui");


$(document).on('load', function (event, target) {

    console.log("DatePicker Setting");
    $(target.target).find("input.date-picker").datepicker({
        format: "yyyy-mm-dd",	//데이터 포맷 형식(yyyy : 년 mm : 월 dd : 일 )
        //startDate: '-10d',	//달력에서 선택 할 수 있는 가장 빠른 날짜. 이전으로는 선택 불가능 ( d : 일 m : 달 y : 년 w : 주)
            language : "en"	//달력의 언어 선택, 그에 맞는 js로 교체해줘야한다.
});

    //$(target.target).find("table.table").tableControl(true);

});


