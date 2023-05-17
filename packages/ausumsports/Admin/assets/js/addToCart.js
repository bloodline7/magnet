const addToCart = {

    init : function (target)
    {
        $(target.target).find(".add-to-cart").each(function () {

        });
    }

}

$(document).on('load', function (event, target) {
    addToCart.init(target);
});
