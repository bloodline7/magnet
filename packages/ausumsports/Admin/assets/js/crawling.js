import $ from "jquery";

require('./jquery.toast');


const crawling = {

    init: function (target)
    {
        console.log('Crawling Init');

        this.target = target;
        this.resultList = [];

        $(this.target).find("#searchSite").unbind().submit(function () {

            const keyword = $(this).find("input[name=keyword]").val();





            if (!keyword) {
                $.toast({
                    icon: 'error',
                    text: "Please Insert Search Keyword",
                    allowToastClose: false,
                    hideAfter: 1000,
                    position: {
                        left: '48%',
                        top: '48%'
                    }
                });
                return false;

            }

            const SearchSite = $(this).find("fieldset.searchSite");


            if (!SearchSite.find('input[type=checkbox]:checked').length) {
                $.toast({
                    icon: 'error',
                    text: "Please select Website for Search",
                    allowToastClose: false,
                    hideAfter: 1000,
                    position: {
                        left: '48%',
                        top: '48%'
                    }
                });
                return false;
            }

            const SearchOption = $(this).find("fieldset.searchOption");

            if(SearchOption.find("[name=scrollbar]").prop("checked"))
            {
                $(crawling.target).find(".searchResult").addClass('scrollbar');
            }
            else {
                $(crawling.target).find(".searchResult").removeClass('scrollbar');
            }



            crawling.resultList = [];

            SearchSite.find('input[type=checkbox]').each(function () {


                let channel = $(this).val();

                if(!channel) {

                    console.error("No Channel");

                    return;


                }
                const targetCard = $("#result_" + channel);

                targetCard.find('.card-title').find('.rows').text(0);
                targetCard.find('div.card-body').find('div.card-text').empty();


                if($(this).prop('checked'))
                {
                    if($(targetCard).attr('data-loading')) {

                        console.log('Loading...');
                        return;
                    }


                    $(targetCard).attr('data-loading', true);

                    targetCard.find('.card-title').find('.keyword').text(keyword);
                    targetCard.show();
                    $.post('/admin/product/crawling/' + channel, { "keyword" : keyword }, function (result) {
                        $(targetCard).removeAttr('data-loading');
                    });
                }
                else {
                    targetCard.hide();
                }
            })
            return false;
        });


        $(this.target).find("[id^=result_]").hide().find(".card-text").empty();

        const userId = $('header:first').attr('data-user');

        window.Echo.channel('private-crawling.'+userId)
            .listen('.crawling', (data) => {

                const Data = JSON.parse(data.search);

                const targetCard = $("#result_" + Data.channel).show();
                const card = targetCard.find('div.card-body').find('div.card-text');

                if($.inArray(Data.detail , crawling.resultList) >= 0) return;
                crawling.resultList.push(Data.detail);
                let ResultRow = crawling.getResultRow(Data);
                card.append(ResultRow);
                let rows = card.find('div.product-box').length;
                targetCard.find('.card-title').find('.rows').text(rows);
            });
    },
    getResultRow: function (Data) {

        return $(document.createElement('div')).addClass('product-box col col-12 col-sm-5 col-md-4 col-lg-4 col-xl-2 border border-secondary p-1')
            .append([
                    $(document.createElement('div')).addClass('imgBox text-center p-0').attr('title', Data.title)
                        .append($(document.createElement('img')).attr({
                            'src': Data.img,
                            'alt': Data.title
                        }).addClass('w-100')),

                    $(document.createElement('div')).addClass('title').text(Data.title),
                    $(document.createElement('div')).addClass('price').text(Data.price),
                    $(document.createElement('div')).addClass('buttons text-center mt-2').append(
                        $(document.createElement('a')).attr({
                            'href': Data.detail,
                            'target': '_blank'
                        }).addClass('detail btn btn-sm bg-primary')
                            .text('Detail link')
                    ),
                ]
            );
    }

}


$(document).on('load', function (event, Target) {

    //console.log(Target);

    $(window).on('socketReady' , function () {

        if (Target.path.toLowerCase() === '/admin/product/crawling') {
            crawling.init(Target.target);
        };

    });



})


