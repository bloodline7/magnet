const BrainTree = {

    init : function (target)
    {
        this.target = $(target);

        this.creditCard();

    },

    creditCard : function ()
    {

        if(this.target.find('.creditCard').length)
        {
            const token = this.target.find('.creditCard:first').attr('data-token');

            $.getScript("https://js.braintreegateway.com/web/dropin/1.8.1/js/dropin.min.js", function()
            {
                braintree.dropin.create({
                    authorization: token,
                    container: '#dropin-container'
                }, function (createErr, instance) {
                   $("#submit-button").click(function ()
                   {
                        instance.requestPaymentMethod(function (err, payload) {



                            $.get('/gceadmin/checkout/process', {payload}, function (response) {
                                if (response.success) {
                                    alert('Payment successfull!');
                                } else {
                                    alert('Payment failed');
                                }
                            }, 'json');
                        });
                    });
                });
            });


        }


    }



}





$(document).on('load', function (event, target) {
    BrainTree.init(target.target);
});



