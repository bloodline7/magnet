import $ from "jquery";
import {router, route, unroute} from 'jqueryrouter';

const modalControl = {

    Init: function (Path) {

        console.log("ModalInit : " + Path);

        this.modal = $("#modal").find(".modal");
        this.body = this.modal.find(".modal-content");

        const modalBody = this.body.find('.modal-body:first');

        this.body.find(".pagination").find("a").each(function () {

            $(this).click(function () {

                modalBody.load($(this).attr('href') + " .modal-body>*", function () {

                    modalControl.Init(Path);

                });

                return false;

            });

        });

        switch (Path) {
            case '/' + window.prefix + '/register':
                this.setAdminRegister();
                break;

            case '/' + window.prefix + '/convention/exhibitor/create':
                this.setExhibitorsCreate();
                break;

            default:
                this.setFormSubmit();
                break;
        }
    },

    setSelectValue: function () {
        this.body.find("form").find("select").each(function () {

            if ($(this).attr('value')) {
                $(this).val($(this).attr('value'));
            }
        });

    },

    onFail : function (xhr, status, error) {

        // console.log(xhr);

        if(xhr.responseText)
        {
            const Result = JSON.parse(xhr.responseText);
            $.toast({
                icon: "error",
                text: Result.message,
                allowToastClose: false,
                hideAfter: 3000,
                position: {
                    left: '48%',
                    top: '48%'
                }
            });
        }


        // modalControl.modalClose();
    },

    setExhibitorsCreate: function () {

        this.setSelectValue();

        if (!this.body.find("form").length) {
            console.error("Form Not found..");
            return false;
        }

        console.log(this.body)
        this.body.find("form").unbind().submit(function () {

            var target = $(this).attr('action');


            console.log(target);
            $.post(target, $(this).serializeArray(), function (Result) {


                console.log(Result);

                $.toast({
                    icon: "success",
                    text: Result.message,
                    allowToastClose: false,
                    hideAfter: 3000,
                    position: {
                        left: '48%',
                        top: '48%'
                    }
                });


                modalControl.modalClose();

            }).fail(modalControl.onFail);

            return false;
        });

        return false;

    },

    showModal: function (Config, exec, onClose) {
        const Con = Config || {};

        if (!$.isFunction(exec)) {
            exec = function () {
                alert('excute');
            }
        }

        $("#modal").html("<div class=\"modal fade\">\n" +
            "        <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">\n" +
            "            <div class=\"modal-content\">\n" +
            "                <div class=\"modal-header\">\n" +
            "                    <h5 class=\"modal-title\">" + Con.title + "</h5>\n" +
            "                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\">\n" +
            "                        <span aria-hidden=\"false\"></span>\n" +
            "                    </button>\n" +
            "                </div>\n" +
            "\n" +
            "                <div class=\"modal-body\">\n" +
            Con.content +
            "                </div>\n" +
            "                <div class=\"modal-footer\">\n" +
            "                    <button type=\"button\" class=\"btn control btn-primary\">" + Con.button + "</button>\n" +
            "                    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Close</button>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "        </div>\n" +
            "    </div>");

        $("#modal").find(".modal").find('button.control').click(function () {
            exec();
        });

        if ($.isFunction(onClose)) {
            $("#modal").find(".modal").on("hidden.bs.modal", function () {
                onClose();
            });
        }

        return $("#modal").find(".modal").modal('show');
    },


    setModalUrl: function (Url, onClose) {

        $("#modal").load(Url + " #modal>*", function () {


            this.modal = $("#modal").find(".modal");
            this.body = this.modal.find(".modal-content");

            this.setFormSubmit();

            if ($.isFunction(onClose)) {
                $("#modal").find(".modal").on("hidden.bs.modal", function () {
                    onClose();
                });
            }
            else
            {
                console.log('Waht', onClose);
            }


            $("#modal").find(".modal").modal('show');

        }.bind(this));


    },

    setCheckBox: function () {
        const form = this.body.find("form");
        form.find("input.checkAll").click(function (e) {
            e.stopPropagation();
            const checked = $(this).prop('checked');
            form.find('input[type=checkbox]').prop('checked', checked).trigger('change');
        });

        form.find('input[type=checkbox]').each(function () {

            $(this).click( function (e) {
                e.stopPropagation();
            });

            $(this).change(function () {
                const checked = $(this).prop('checked');
                $(this).parents('tr:first').find("input[type=text]").prop('disabled', !checked);

                if (checked)
                {
                    $(this).parents('tr:first').addClass('selected');
                    $(this).parents('tr:first').find("input[type=text]").focus();
                }
                else
                {
                    $(this).parents('tr:first').removeClass('selected');

                }




            });


            $(this).parents('tr:first').unbind().click( function (e) {
                e.stopPropagation();

                const checked = !$(this).prop('checked');

                $(this).prop('checked', checked).trigger('change');

            }.bind(this));



        });

    },

    setFormSubmit: function () {

        this.setSelectValue();
        this.setCheckBox();


        this.body.find("form").each( function () {

            const Method = $(this).attr('method') || 'POST';

            const method =Method.toLowerCase();

            switch (method) {

                case 'get' :

                    $(this).unbind().submit(function () {

                        var target = $(this).attr('action');

                        $('.modal-body:first').load(target + " .modal-body > *",  $(this).serialize(), function () {
                            modalControl.setFormSubmit();
                        });
                        return false;
                    });

                    break;
                default :
                    $(this).unbind().submit(function () {

                        if(typeof $(this).get(0).beforeSubmit === 'function')
                            $(this).get(0).beforeSubmit();

                        var target = $(this).attr('action');
                        $.post(target, $(this).serializeArray(), function (Result) {

                            $.toast({
                                icon: "success",
                                text: Result.message,
                                allowToastClose: false,
                                hideAfter: 3000,
                                position: {
                                    left: '48%',
                                    top: '48%'
                                }
                            });

                            modalControl.modalClose();
                        }).fail(modalControl.onFail);
                        return false;
                    });

                    break;
            }
        });

    },

    setAdminRegister: function () {
        this.setFormSubmit();

        //this.modalClose();

    },

    modalClose: function () {
        $("#modal").find(".modal").modal('hide');
    }


}

window.modalControl = modalControl;


$(document).on('load', function (event, target) {

    $(target.target).find("a").each(function () {

        if ($(this).attr('target') == 'modal') {
            $(this).attr('target', "#modal");

            $(this).unbind().click(function () {

                let link = $(this).attr('href');


                $("#modal").load(link + " .modal", function () {

                    $("#modal").find('.modal').modal('show').on('hidden.bs.modal', function () {

                        console.log('Popup close');

                        const query = window.location.search;

                        console.log(window.location.pathname + query);

                        router.set(window.location.pathname + query, true, false);

                    }.bind(this));

                    $(document).trigger('load', {'target': "#modal", 'path': link});

                }.bind(this));

                return false;
            });


        }


    });


    if ($(target.target).attr('id') === 'modal') {
        modalControl.Init(target.path);
    }
});
