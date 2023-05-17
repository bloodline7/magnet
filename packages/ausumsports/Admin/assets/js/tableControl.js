import $ from "jquery";

import 'jquery-ui/ui/widgets/sortable'

import {router, route, unroute} from 'jqueryrouter';


require('./jquery.hotkeys');
require('./jquery.toast');


(function ($) {
    jQuery.fn.tableControl = function (buttonOnly) {
        tableControl.Init(this, buttonOnly);
        return this;
    };
})(jQuery);

const tableControl =
    {
        Init: function (Table, buttonOnly) {
            this.Table = $(Table);
            this.isLast = true;
            if (buttonOnly) {

                $(document).unbind('keydown');
                this.setButtonOnly();
                return;
            }

            this.setColNumber();
            this.setColGroup();
            this.setData();
            this.setBodyEdit();
            this.setEditBt();
            this.setScroll();
            this.setSortable();

            this.getUrl();
            this.setGlobalBt();

            this.setGuide();
        },

        addButton : function (text, onClick)
        {
            const Button = $(document.createElement('button')).text(text);

            Button.appendTo( $(document.createElement('div')).appendTo(this.guideLayer) );

            Button.bind('click', onClick);

           return Button;
        },
        setGuide: function ()
        {

          const guideBt = $(document.createElement('div')).addClass('table-control-bt');

            guideBt.insertBefore(this.Table);
            const Button = $(document.createElement('button')).text('Guide').appendTo(guideBt);
            const boxLayer = $(document.createElement('div')).addClass('boxLayer').appendTo(guideBt);
            const guideLayer = this.guideLayer = $(document.createElement('div')).addClass('guideLayer').appendTo(boxLayer);

            this.addButton('Insert [Ctrl+Insert]', function (e) {
                e.stopPropagation();
                this.appendNewRecord();
            }.bind(this)).addClass('btn btn-sm btn-outline-info');

            this.addButton('Delete [Ctrl+Delete]', function (e) {
                e.stopPropagation();
                this.deleteRecord($(this.Table).find('tbody').find('tr.selected'));
            }.bind(this)).addClass('btn btn-sm btn-outline-danger');

            this.addButton('Save [Ctrl+S]', function (e) {
                e.stopPropagation();
                this.updateData();
            }.bind(this)).addClass('btn btn-sm btn-outline-warning');


            this.addButton('Merge to [Ctrl+M]', function (e) {
                e.stopPropagation();

                this.mergeData();

            }.bind(this)).addClass('btn btn-sm btn-outline-success');




            this.Table.click ( function () {
                guideLayer.fadeOut();
            });

            guideBt.click(function () {
                guideLayer.fadeOut();
            });

            Button.click(function (e)  {

                e.stopPropagation();
                guideLayer.toggle();

            });

        },
        setSortable : function ()
        {
            const Sort = $(this.Table).find("thead:first").find("th.sort")

            if(!$(Sort).length) return;

            const dataNo = Sort.attr('data-num');
            const tbody =  $(this.Table).find("tbody:first");


            tbody.sortable({
                axis: "y",
                containment: "parent",
                tolerance: "pointer"
            });

            tbody.on( "sortupdate", function( event, ui ) {
                let no = 1;
                tbody.find("tr").each( function () {
                    const td = $(this).find("[data-num='"+dataNo+"']");
                    $(td).removeAttr('data-edit').attr('data-value', no).empty().html(no);
                    tableControl.checkUpdate(this);
                    no++;
                });
            });
        },
        setButtonOnly: function () {

            const url = $(this.Table).attr('path') || window.location.pathname;
            $(this.Table).find("tbody").find(".delete,.remove").each(function () {

                $(this).click(function () {
                    $(this).parents('tr:first').addClass('selected');
                    modalControl.showModal({
                            'title': 'Remove Confirm',
                            'content': 'Do you wanna remove this row?',
                            'button': 'Remove'
                        },
                        function () {
                            const pk = $(this).parents('tr:first').attr('data-pk')

                            $.delete(url + "/" + pk, function (Result) {

                                $.toast({
                                    icon: 'success',
                                    text: Result.message,
                                    allowToastClose: false,
                                    hideAfter: 2000,
                                    position: {left: '48%', top: '48%'}
                                });

                                modalControl.modalClose();
                                const query = window.location.search;
                                router.set(window.location.pathname+query, false, false);

                            });



                        }.bind(this),
                        function () {

                            $(this).parents('tr:first').removeClass('selected');

                        }.bind(this));
                });
            });

            $(this.Table).find("tbody").find("a").each(function () {

                const target = $(this).attr('target');

                switch (target) {

                    case 'modal' :
                        $(this).unbind().click( function () {

                            $(this).parents('tr:first').addClass('selected');


                            const link = $(this).attr('href');

                            $("#modal").load(link + " .modal", function () {

                                $("#modal").find('.modal').modal('show').on('hidden.bs.modal', function () {

                                    $(this).parents('tr:first').removeClass('selected');


                                    const query = window.location.search;
                                    router.set(window.location.pathname+query, false, false);

                                }.bind(this));

                                $(document).trigger('load', {'target': "#modal", 'path': link});

                            }.bind(this));

                            return false;
                        });
                        break;
                }
            });
        },

        /**
         * Colgroup 너비를 thead th 에 설정함 ( for tbody scroll)
         * @returns {boolean}
         */
        setColGroup: function () {
            if (this.Table.find('colgroup').length) return false;

            const wit = Math.round(100 / this.Table.find('thead > tr').find('th').length, 2);

            this.Table.find('thead > tr').find('th').each(function () {
                $(this).attr('width', wit + '%');
            });

            return true;
        },


        /**
         * 화면 포커스 시 단축키 셋팅
         */
        setGlobalBt: function () {
            var Obj = this;

            $(document).unbind('keydown').bind('keydown', 'ctrl+insert', function (event) {
                event.preventDefault();
                Obj.appendNewRecord();

            }).bind('keydown', 'ctrl+del', function (event) {
                event.preventDefault();
                Obj.deleteRecord($(Obj.Table).find('tbody').find('tr.selected'));

            }).bind('keydown', 'ctrl+s', function (event) {
                event.preventDefault();
                Obj.updateData();
            })


        },

        /**
         * 페이징 처리
         * @param Page
         * @param callBack
         * @returns {boolean}
         */
        setTableBody: function (Page, callBack) {
            if (this.isLast) {
                if ($.isFunction(callBack)) {
                    callBack(true);
                }

                return false;
            }

            var Page = Page || 0;
            var Path = window.location.pathname;
            var Url = Path;

            if (Page) this.Page = this.Page + Page;

            $.get(Url, {Sort: this.SortList, page: this.Page}, function (Data) {
                var Tbody = this.Table.find('tbody');

                var Rows = $(Data).find("table:first").find('tbody').find('tr');

                if (Rows.length == 0) {
                    this.isLast = true;

                    if ($.isFunction(callBack)) {
                        callBack(true);
                    }

                    return false;
                }

                Tbody.append(Rows);

                var sorting = Page ? false : true;
                //sorting = false;


                this.Table.trigger("update", [sorting, function () {
                    this.setData(Rows);
                    this.setBodyEdit(Rows);
                    this.setEditBt(Rows);

                    //if (!Page) this.setScroll();

                    if ($.isFunction(callBack)) {
                        callBack();
                    }

                }.bind(this)]);

            }.bind(this));

        },

        reSortBody: function () {
            if (!$(this.Table)[0].config) return;

            this.lastSortList = $(this.Table)[0].config.sortList;
            var sortList = this.lastSortList;

            var fnObj = this.Fn;
            var SortList = [];

            for (var k in sortList) {
                var val = sortList[k];

                if ($.isFunction(val)) continue;

                var index = val[0];
                var Fn = fnObj[index];
                var List =
                    {
                        'FNAME': Fn.fn,
                        'SORT': val[1] == 1 ? 'DESC' : 'ASC'
                    };

                SortList.push(List);
            }

            this.Page = 1;
            this.SortList = SortList;
            this.setTableBody();


        },

        onResize: function () {

        },
        setScroll: function () {
            var wrap = this.Table.parents("div:first");

            if (!$(this.Table).hasClass('scroll')) {
                return;
            }


            console.log('Setting Scroll');
            console.log(wrap.height(), this.Table.height());


            if (wrap.height() < this.Table.height()) {
                console.log('wrapping');
                this.Table.find('thead').headerFix(wrap, this.onResize);
            }
        },


        /**
         * 셀 데이터 초기화
         * @param Rows
         */
        setData: function (Rows) {
            var Rows = Rows || this.Table.find('tbody').find('tr');


            for (var Num in this.Fn) {
                var Obj = this.Fn[Num];

                //if (!Obj.fn) continue;

                if (Obj.fc.attr('aria-required')) {
                    Rows.find('td:eq(' + Num + ')').each(function () {
                        $(this).attr('aria-required', 'true');
                    });
                }

                switch (Obj.ft) {

                    case 'selectBox' :
                        var fdata = Obj.fdata;
                        Rows.find('td:eq(' + Num + ')').each(function () {
                            var Value = $(this).attr('data-value') || $(this).text().trim();
                            $(this).attr('data-org', Value);
                            $(this).attr('data-value', Value);
                            var psValue = fdata[Value];
                            $(this).empty().text(psValue);

                        });
                        break;

                    case 'toggle' :

                        Rows.find('td:eq(' + Num + ')').each(function () {
                            var Value = $(this).attr('data-value') || $(this).text().trim();
                            $(this).attr('data-org', Value);
                            $(this).attr('data-value', Value);

                            $(this).empty().append(tableControl.getToggleBt($(this)));
                        });
                        break;


                    case 'checkBox' :

                        Rows.find('td:eq(' + Num + ')').each(function () {
                            var Value = $(this).attr('data-value') || $(this).text().trim();
                            $(this).attr('data-org', '');
                            $(this).attr('data-value', '');
                            $(this).attr('data-checked', Value);

                            $(this).empty().append(tableControl.getCheckBox($(this)));
                        });
                        break;

                    default :

                        Rows.find('td:eq(' + Num + ')').each(function () {
                            $(this).attr('data-org', $(this).text().trim());

                        });
                        break

                }
            }
        },
        checkUpdate: function (Tr) {
            var diff = 0;

            $(Tr).find("td[data-value]").each(function () {

                if ($(this).attr('data-org') !== $(this).attr('data-value')) {
                    diff++;
                }
            });


            if (diff) {
                $(Tr).addClass('changed');
            } else {
                $(Tr).removeClass('changed');
            }
        },

        deleteRecord: function (Target) {
            if (!Target.attr("data-pk")) {
                Target.nextAll("tr:first").find('td:first').click();
                Target.fadeOut(function () {
                    $(this).remove();
                });
                return true;
            }

            var Param = this.getRowData(Target, true);

            if (!confirm('Are You really Delete this Row?' + "\n\n\n" + JSON.stringify(Param))) return false;

            var Url = this.Url + '/' + Target.attr("data-pk");

            $.delete(Url)
                .done(function (Result) {

                    Target.nextAll("tr:first").find('td[data-org]:first').click();

                    $(Target).remove();

                    $.toast({
                        icon: 'success',
                        text: Result.message,
                        allowToastClose: false,
                        hideAfter: 800,
                        position: {left: '48%', top: '48%'}
                    });


                })
                .fail(function (xhr, status, error) {

                    const errorMsg = {
                        'xhr': xhr,
                        'status': status,
                        error: error
                    };

                    console.error(errorMsg);

                    $.toast({
                        icon: 'error',
                        text: xhr.responseJSON.message,
                        allowToastClose: false,
                        hideAfter: 800,
                        position: {
                            left: '48%',
                            top: '48%'
                        }
                    });


                });

            return;
        },

        setEditBt: function (Rows) {

            const Obj = this;
            const InsertBt = this.Table.find('.insert');
            InsertBt.unbind().click(function () {
                this.appendNewRecord();
            }.bind(this));

            Rows = Rows || $(this.Table).find('tbody');

            Rows.find('.delete').removeClass('delete').unbind().click(function () {
                const Target = $(this).parents("tr:first");
                Obj.deleteRecord(Target);
            });

        },

        /**
         * 새로운 편집 행을 추가한다
         * @returns {boolean}
         */
        appendNewRecord: function () {

            var Fn = this.Fn;
            const Row = $(document.createElement('tr')).prependTo(this.Table.find('tbody'));

            let Num = 0;
            $(this.Table).find('thead>tr:first').find('th,td').each(function () {

                const Td = $(document.createElement('td')).attr('data-num', parseInt(Num, 10)).appendTo(Row);
                if ($(this).attr('aria-required')) Td.attr('aria-required', true);

                if (!Fn[Num].fn) {
                    if ($(this).text().toLowerCase() === 'delete') {
                        $(document.createElement('button')).attr('type', 'button').addClass('delete btn btn-danger btn-sm').text('Delete').appendTo(Td);
                    }
                }

                Num++;
            });


            this.setData(Row);
            this.setBodyEdit(Row);
            this.setEditBt(Row);
            Row.find('td[data-fn]:first').click();

            return true;
        },


        /**
         * 테이블 Head 정보를 읽어 셀 넘버와 데이터를 설정함
         */
        setColNumber: function () {

            let num = 0,
                Fn = [],
                Obj = this;


            this.Table.find('thead > tr').find('th').each(function () {
                $(this).attr('data-num', num);

                var sFn =
                    {
                        fn: $(this).attr('data-fn') || null, // DB field name
                        ft: $(this).attr('data-ft') || 'text', // form type
                        fc: $(this)
                    };

                if ($(this).attr('data-fget')) {
                    sFn.fdata = JSON.parse($(this).attr('data-fget'));
                }

                if ($(this).attr('data-fget-url')) {

                    console.log($(this).attr('data-fget-url'));

                    $.ajax({
                        url: $(this).attr('data-fget-url'),
                        global: false,
                        type: 'GET',
                        async: false, //blocks window close
                        success: function (Result) {
                            sFn.fdata = Result;
                        }
                    });


                    // $.ajax($(this).attr('data-fget-url'), )
                    // sFn.fdata = JSON.parse($(this).attr('data-fget'));
                }


                if (sFn.fn) {
                    $(this).click(function () {
                        Obj.reSortBody();

                    }.bind(this));
                }

                //log(sFn);

                Fn[num] = sFn;
                num++;
            });

            this.Fn = Fn;


        },


        getRowData: function (Row, del) {

            var Obj = this;
            var Param = {};

            var del = del || false;

            var Pk = $(Row).attr('data-pk') || null;

            if (Pk) Param.PK = Pk;

            $(Row).find('td[data-num]').each(function () {

                var Num = parseInt($(this).attr('data-num'), 10);
                var Key = Obj.Fn[Num].fn;
                var Type = Obj.Fn[Num].ft;
                var Title = Obj.Table.find('thead').find('th').eq(Num).text().trim();


                console.log(Type);
                switch (Type) {
                    case 'toggle' :
                    case 'checkBox' :
                    case 'selectBox' :
                        var value = $(this).attr("data-value");
                        break;

                    default :
                        var value = $(this).text().trim();
                        break;
                }


                var require = $(this).attr('aria-required') || false;

                if ((require === 'true') && (!value)) {

                    if (del) return false;
                    $(Row).find('td:eq(' + Num + ')').click();
                    Param = false;

                    var Top = $(this).offset().top + $(this).outerHeight();
                    var Left = $(this).offset().left;

                    $.toast({
                        icon: 'error',
                        text: Title + ' Required.',
                        allowToastClose: false,
                        hideAfter: 1000,
                        position: {left: Left + 'px', top: Top + 'px'}
                    });
                    $(this).click();

                    return false;
                }

                if (Key) {
                    Param[Key] = value;
                }
            });


            this.setColorBt();


            return Param;
        },

        getUrl: function () {

            if (this.Table.attr('data-path')) {
                this.Url = this.Table.attr('data-path');
                return;
            }

            this.Url = window.location.pathname;
            return;
        },

        mergeData :function () {

            const CheckItem = this.Table.find("[data-checked]").find("input:checkbox:checked");

            if (CheckItem.length < 1) {

                $.toast({
                    icon: 'error',
                    text: "Please Check Item to Merge",
                    allowToastClose: false,
                    hideAfter: 800,
                    position: {
                        left: '48%',
                        top: '48%'
                    }
                });

                return false;
            }


            var checkedItem = [];

            CheckItem.each( function () {
                checkedItem.push($(this).val());
                $(this).attr('disabled', 'disabled');
            });


            $.toast({
                icon: 'info',
                text: "Click CheckBox of target Item to Merge",
                allowToastClose: true,
                hideAfter: 5000,
                position: {
                    left: '48%',
                    top: '48%'
                }
            });



            this.Table.find("[data-checked]").find("input:checkbox").addClass('active').unbind().click(function () {




            });


            console.log(checkedItem);



        },

        updateData: function (callBack) {
            var callBack = callBack || null;

            var Obj = this;

            if (this.Table.find('tr.changed').length < 1) {
                if ($.isFunction(callBack)) callBack();
                return false;
            }

            var reload = false;


            this.Table.find('tr.changed').each(function () {
                var Param = Obj.getRowData(this);
                if (!Param) return false;

                console.log('ObjUrl', Obj.Url);

                if (Param.PK) {
                    $.post(Obj.Url + '/' + $(this).attr("data-pk"), Param)
                        .done(function (Result) {

                            console.log(Result);

                            $(this).find("td[data-value]").each(function () {
                                $(this).attr('data-org', $(this).attr('data-value'));
                            });
                            $(this).removeClass('changed');


                            $.toast({
                                icon: 'success',
                                text: Result.message,
                                allowToastClose: false,
                                hideAfter: 800,
                                position: {
                                    left: '48%',
                                    top: '48%'
                                }
                            });
                        }.bind(this))

                        .fail(function (xhr, status, error) {

                            var error = {
                                'xhr': xhr,
                                'status': status,
                                error: error
                            };

                            console.error(error);

                            $.toast({
                                icon: 'error',
                                text: xhr.responseJSON.message,
                                allowToastClose: false,
                                hideAfter: 800,
                                position: {
                                    left: '48%',
                                    top: '48%'
                                }
                            });


                        });
                } else {

                    reload = true;

                    console.log(Obj.Url);


                    $.put(Obj.Url, Param)
                        .done(function (Result) {
                            if (Result.PK) {
                                $(this).attr('data-pk', Result.PK);
                            }

                            $(this).find("td[data-value]").each(function () {
                                $(this).attr('data-org', $(this).attr('data-value'));
                            });
                            $(this).removeClass('changed');

                            $.toast({
                                icon: 'success',
                                text: Result.message,
                                allowToastClose: false,
                                hideAfter: 800,
                                position: {
                                    left: '48%',
                                    top: '48%'
                                }
                            });

                        }.bind(this))
                        .fail(function (xhr, status, error) {
                            reload = false;

                            var error = {
                                'xhr': xhr,
                                'status': status,
                                error: error
                            };

                            console.error(error);

                            $.toast({
                                icon: 'error',
                                text: xhr.responseJSON.message,
                                allowToastClose: false,
                                hideAfter: 800,
                                position: {
                                    left: '48%',
                                    top: '48%'
                                }
                            });


                        });
                }
            });

            this.Table.find('tr.changed').promise().done(function () {


                if (Obj.Table.hasClass('tableSorter')) {
                    $(Obj.Table).trigger("update");
                    //$(Obj.Table).trigger("appendCache");

                }

                //if ($.isFunction(callBack)) callBack();

                //if(reload) window.location.reload();

            });


        },


        getSelectBox: function (Data, val) {
            var form = $(document.createElement('select'));

            //  var Data = JSON.parse(Data);

            for (var k in Data) {
                var opt = $(document.createElement('option')).val(k).text(Data[k]).appendTo(form);

                if (k == val) {
                    opt.attr('selected', 'selected');
                }
            }

            form.val(val);

            return form;
        },

        getCheckBox: function (Cell) {


            var value = (typeof Cell == "string") ? Cell : Cell.attr('data-checked');

            var form = $(document.createElement('div'));

            var input = $(document.createElement('input')).attr('type', 'checkbox').val(value).addClass('form-check-input').appendTo(form);

            var Obj = this;
            input.click(function () {

                var  Value = ($(this).prop('checked')) ? $(this).val() : '';
                $(Cell).removeAttr('data-edit').attr('data-value', Value);
                Obj.checkUpdate($(Cell).parents("tr:first"));
            });

            return form;
        },

        getToggleBt: function (Cell) {
            var value = (typeof Cell == "string") ? Cell : Cell.attr('data-value');
            var form = $(document.createElement('div')).addClass('form-switch');
            var input = $(document.createElement('input')).attr('type', 'checkbox').val(1).addClass('form-check-input').appendTo(form);

            if (Number.parseInt(value) > 0) {
                input.attr('checked', 'checked')
                $(Cell).attr('data-org', 1).attr('data-value', 1);
            }

            var Obj = this;
            input.click(function () {
                var  Value = ($(this).prop('checked')) ? '1' : '0';
                $(Cell).removeAttr('data-edit').attr('data-value', Value);
                Obj.checkUpdate($(Cell).parents("tr:first"));
            });

            return form;
        },



        setToggleBt: function (Td) {
            this.getToggleBt(Td.text()).appendTo(Td);
            Td.attr('align', 'center');
        },

        setColorBt: function () {


        },

        setEditable: function (Cell) {

            this.Table.find('tr.selected').removeClass('selected');
            $(Cell).parents('tr:first').addClass('selected');


            var Num = $(Cell).attr('data-num') || -1;
            if (Num < 0) return false;

            if ($(Cell).attr('data-edit')) return false;

            var Fn = this.Fn;
            if (!Fn[Num].fn) return false;

            var Type = Fn[Num].ft;


            switch (Type) {
                case 'selectBox' :
                    var Data = Fn[Num].fdata;

                    var Value = $(Cell).attr('data-value');
                    var Input = this.getSelectBox(Data, Value);

                    $(Cell).empty().append(Input);

                    break;

                case 'checkBox' :


                    return;

                    break;

                case 'toggle' :
                    //var Data = Fn[Num].fdata;

                    return;

                    var Value = $(Cell).attr('data-value');
                    var Input = this.getToggleBt(Value);

                    //$(Cell).empty().append(Input);
                    Input = Input.find("input:first");



                    console.log("tpe : toggle");


                    break;


                case 'date' :
                    var Value = $(Cell).text().trim();
                    var Input = $(document.createElement('input')).attr('type', 'date');

                    $(Cell).empty().append(Input);

                    break;

                case 'datetime' :

                    var Value = moment($(Cell).text().trim()).format(moment.HTML5_FMT.DATETIME_LOCAL);


                    var Input = $(document.createElement('input')).attr('type', 'datetime-local');
                    $(Cell).empty().append(Input);

                    break;

                default:

                    var Value = $(Cell).text().trim();
                    var Input = $(document.createElement('input')).attr('type', 'text');

                    $(Cell).empty().append(Input);

                    break;

            }


            var Obj = this;

            var Table = this.Table;

            Input.unbind().focus().val(Value);

            //console.log(Input.val());


            //this.setFocusScroll(Input);

            Input.blur(function () {
                var Value = $(this).val();

                var Cell = $(this).parents('td:first');
                var tagName = $(this).get(0).tagName.toLowerCase();

                if ($(this).attr('type') == 'datetime-local') {
                    if (Value) Value = moment(Value).format("YYYY-MM-DD HH:mm:ss");
                }

                if ($(this).attr('type') == 'checkbox') {
                    Value = ($(this).prop('checked')) ? 1 : 0;

                    console.log('CheckBox :' + Value);
                }


                switch (tagName) {
                    case 'select':
                        var pValue = $(this).find('option:selected').text();
                        break;


                    default:


                        var pValue = Value;
                        break;
                }
                $(Cell).removeAttr('data-edit').attr('data-value', Value).empty().html(pValue);
                Obj.checkUpdate($(Cell).parents("tr:first"));
            });


            var goUp = function (Obj) {

                const targetCell = $(Obj).parents('tr:first').prevAll('tr:first').find('td:eq(' + Num + ')');
                if (targetCell.length) {
                    targetCell.click();
                }
            }


            Input.bind('keydown', 'down', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var thisRow = $(this).parents('tr:first');
                var thisTbody = $(this).parents('tbody:first');

                if ($(this).parents('tr:first').nextAll('tr:first').length) {
                    $(this).parents('tr:first').nextAll('tr:first').find('td:eq(' + Num + ')').click();
                } else {

                    var cb = function (isLast) {
                        if (isLast) {
                            thisRow.find('td:eq(' + Num + ')').click();
                        } else {
                            thisRow.next().find('td:eq(' + Num + ')').click();
                        }
                    };

                    this.blur();

                    Obj.setTableBody(1, cb);
                }

            }).bind('keydown', 'ctrl+down', function (event) {
                event.preventDefault();

                if ($(this).parents('tr:first').nextAll('tr:first').length) {
                    $(this).parents('tr:first').nextAll('tr:first').find('td:eq(' + Num + ')').click();
                } else {
                    var thisRow = $(this).parents('tr:first');
                    var cb = function () {
                        thisRow.nextAll('tr:first').find('td:eq(' + Num + ')').click();
                    };
                    this.blur();
                    Obj.setTableBody(1, cb);
                }

            }).bind('keydown', 'up', function (event) {
                event.preventDefault();
                goUp(this);

            }).bind('keydown', 'ctrl+up', function (event) {
                event.preventDefault();
                goUp(this);

            }).bind('keydown', 'tab', function (event) {
                event.preventDefault();
                $(this).parents('td:first').next().click();

            }).bind('keydown', 'ctrl+right', function (event) {
                event.preventDefault();
                $(this).parents('td:first').next().click();

            }).bind('keydown', 'shift+tab', function (event) {
                event.preventDefault();
                $(this).parents('td:first').prev().click();

            }).bind('keydown', 'ctrl+left', function (event) {

                event.preventDefault();
                $(this).parents('td:first').prev().click();

            }).bind('keydown', 'ctrl+insert', function (event) {
                event.preventDefault();
                $(this).blur();
                Obj.appendNewRecord();
            }).bind('keydown', 'ctrl+del', function (event) {
                event.preventDefault();
                Obj.deleteRecord($(this).parents('tr:first'));

            }).bind('keydown', 'ctrl+m', function (event) {
                event.preventDefault();
                Obj.mergeData();

            }).bind('keydown', 'return', function (event) {
                event.preventDefault();

                var Target = $(this).parents('tr:first').nextAll('tr:first').find('td:eq(' + Num + ')');
                var callBack = function () {

                    if (Target.length) Target.click();
                    else $(this).click();

                }.bind(this);

                $(this).blur();
                Obj.updateData(callBack);

            }).bind('keydown', 'ctrl+s', function (event) {
                event.preventDefault();
                var td = $(this).parent();
                $(this).blur();
                Obj.updateData();
                td.click();
            });

            $(Cell).attr('data-edit', true);
        },

        initRows: function (Row) {
            var Obj = this;

            var num = 0;

            $(Row).find('td').each(function () {
                $(this).attr('data-num', num).click(function () {
                    Obj.setEditable(this);
                });
                num++;

            });

        },

        setBodyEdit: function (Rows) {
            var Obj = this;
            var Rows = Rows || this.Table.find("tbody > tr");

            Rows.each(function () {
                Obj.initRows(this);
            });
        }
    };


$(document).on('load', function (event, target) {

    $(target.target).find("table.table").tableControl(true);

    $(target.target).find("table.tableControl").tableControl();


});


