
import {router, route, unroute} from 'jqueryrouter';

const contentUpdater = {

    main: '/admin',
    target: "#withMenus",
    loadingClass: 'loading',
    routerGroup: {},
    routeList: [],
    init: function () {

        console.log('init Start..');

        /** loading **/
        this.setBeforeUpdate(this.target);
        this.setEndUpdate(this.target);
        this.setPageLinks();
        this.callPageTrigger();

        this.setSelfLink();

        //this.checkLogin();
    },

    setSelfLink: function () {
        const query = window.location.search;

        const link = window.location.pathname;
        const loadTarget = ($("#list").length) ? '#list' : "#content";

        this.setRouter(link, loadTarget);

    },

    /**
     * 상단 , 좌측 메뉴의 이벤트 정의
     */
    setPageLinks: function () {

        router.init();

        this.setTargetLink("header", function (target, link) {
            const redirect = $(target).find("#sideMenu").find("a.active").attr('href');
            if (redirect)
            {
                if(redirect !== window.location.pathname)
                {
                    console.warn("target Link not matched.\n Auto Replace to ->" +  redirect);
                    router.set({route: redirect}, false, false);
                }
            }
        });
        this.setTargetLink("#sideMenu")
        this.setTargetLink("#content");
    },

    /**
     * 링크 클릭 시 링크 활성화 표시
     * @param button
     * @param target
     */
    afterClick: function (button, target) {

        $(target).find("a").removeClass('active');
        $(button).addClass('active');

        console.log("afterLink Href : " + $(button).attr('href'));

        if($(button).parents(".collapse").length)
        {
            const id = $(button).parents(".collapse:first").addClass('show').attr('id');

            $(target).find("[href='#"+id+"']").addClass('active');
        }
        else
        {
            $(target).find(".collapse").removeClass('show');
        }


        if ($(button).parents("li.dropdown").length) {
            $(button).parents("li.dropdown").find("a.dropdown-toggle").addClass('active');
        }
    },

    setTargetLink: function (target, onLoad) {

        // A Link SETTING
        $(target).find("a").each(function () {
            const link = $(this).attr('href') || "#";

            if (link == '#') return;
            if ($(this).attr('download')) return;

            const loadTarget = $(this).attr('target') || contentUpdater.getTarget(target, this);

            if (loadTarget == "modal") return;

            if (!$(loadTarget).length) {
                console.error("Target : " + loadTarget + " Not found");
                return;
            }

            contentUpdater.setRouter(link, loadTarget, onLoad);
            $(this).unbind().click(function () {
                try {

                    router.set(link, false, false);
                    contentUpdater.afterClick(this, target);

                } catch (e) {
                    console.error(e);
                    console.log("Link of " + target + ' to ->' + loadTarget);
                    console.log("Link :" + link);
                    return false;
                }


                return false;
            });
        });

        // FORM SETTING
        $(target).find("form").each(function () {
            const link = $(this).attr('action') || window.location.pathname;
            const method = $(this).attr('method') || "get";

            if (method === 'get') {
                const loadTarget = $(this).attr('target') || contentUpdater.getTarget(target, this);

                console.warn('new Target:', loadTarget);

                contentUpdater.setRouter(link, loadTarget, onLoad);
                $(this).submit(function () {
                    router.set({route: link, queryString: $(this).serialize()}, true, false);
                    return false;
                });
            } else {

                const Success = function (Result) {
                    $.toast({
                        icon: "success",
                        text: Result.message,
                        allowToastClose: false,
                        hideAfter: 3000,
                        position: {left: '48%', top: '48%'}
                    });
                    router.set(window.location.pathname, false, false);
                };

                const Fail = function (Result) {
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
                };


                $(this).submit(function () {

                    if ($(this).find("input[type=file]").length) {

                        var form = $(this)[0];
                        var formData = new FormData(form);
/*
                        $(this).find("input[type=file]").each( function () {
                            formData.append($(this).attr('name'), $(this)[0].files[0]);
                        });*/

                        $.ajax({
                            url : link,
                            type : 'POST',
                            data : formData,
                            processData: false,  // tell jQuery not to process the data
                            contentType: false,  // tell jQuery not to set contentType
                            success : Success,
                            error : Fail
                        });

                    } else {
                        $.post(link, $(this).serializeArray(), Success
                        ).fail(Fail);
                    }
                    return false;
                });
            }
        });


        $(target).find("select.links").each(function () {

            const loadTarget = $(this).attr('target') || contentUpdater.getTarget(target, this);

            $(this).find('option').each(function () {
                if ($(this).attr('value')) {
                    const link = $(this).attr('value') || window.location.pathname;
                    contentUpdater.setRouter(link, loadTarget, onLoad);
                }
            });

            $(this).unbind().change(function () {
                const link = $(this).val();
                router.set(link, false, false);
            });
        });

    },


    /* checkLogin: function () {
         const link = '/' + window.prefix + '/login';
         const groupFunction = this.getRouterGroup('modal');
         groupFunction(link, "#content", function () {
         });

     },*/

    /**
     * 리로딩 시 메뉴 초기화 처리를 한다.
     */
    callPageTrigger: function () {
        const link = window.location.pathname;
        let linkArray = link.split('/');
        $("header").find("a.active").removeClass('active');

        $("#sideMenu").find("a.active").removeClass('active');

        for (let len = linkArray.length; len > 1; len--) {
            const linkStr = linkArray.join('/');

            if (!$("header").find("a.active").length) {

                $("header").find("a").each(function () {

                    if ($(this).attr('href') == linkStr)
                        contentUpdater.afterClick(this, "header");
                });
            }

            if (!$("#sideMenu").find("a.active").length) {
                $("#sideMenu").find("a").each(function () {

                    if ($(this).attr('href') == linkStr) {
                        contentUpdater.afterClick(this, "#sideMenu");
                        return false;
                    }

                });
            }

            linkArray.pop();
        }

        $(document).trigger('load', {'target': this.target, 'path': link});

    },

    /**
     * Router Define
     * @param link
     * @param target
     */
    setRouter: function (link, target, onload) {


        if ($.inArray(link, this.routeList) < 0) {
            route(link, function (e, param, query) {
                const url = link + $.param(query) ? '?' + $.param(query) : '';
                this.load(url, $(target), function () {

                    this.setTargetLink(target);

                    if ($.isFunction(onload)) onload(target, link);

                    $(document).trigger('load', {'target': target, 'path': link});

                }.bind(this));
            }.bind(this));

            this.routeList.push(link);
            return;
        }

    },

    /**
     * 작업중
     * @param target
     * @param Obj
     * @returns {*|jQuery|string}
     */
    getTarget: function (target, Obj) {
        if ($(Obj).attr('target')) return $(Obj).attr('target')

        switch (target) {
            case 'header' :
                return '#withMenus';

            case '#withMenus' :
                return '#content';


            case "#sideMenu" :
                return "#content";

            case "#content" :

                if ($(target).find("#list").length)
                    return "#list"
                else
                    return "#content";
                break;

            case "#list" :
                return "#list";
                break;

            default:
                console.warn("target Not Found from " + target);
                return "#withMenus";
                break;

        }

    },
    setBeforeUpdate: function (target, beforeUpdate) {
        beforeUpdate = (typeof beforeUpdate === 'function') ? beforeUpdate : function (event, target) {
            if ($("body:first").hasClass(this.loadingClass)) return;
            $("body:first").addClass(this.loadingClass);
            $(target).addClass(this.loadingClass);
            $("#loading:first").clone(true).show().appendTo($(target).parent());
        }.bind(this);


        $(target).unbind('beforeUpdate').on('beforeUpdate', beforeUpdate);
    },
    setEndUpdate: function (target, endUpdate) {


        endUpdate = (typeof endUpdate === 'function') ? endUpdate : function (event, target) {
            $("body:first").removeClass(this.loadingClass);
            $(target).removeClass(this.loadingClass);
            $(target).parent().find("#loading").remove();
        }.bind(this);

        $(target).unbind('endUpdate').on('endUpdate', endUpdate);
    },
    /**
     * Jquery Load Function 을 이용하여 컨텐츠를 로딩한다
     * 성공시 endUpdate trigger 발생함
     * @param Url
     * @param Target
     * @param afterUpdate
     * @returns {boolean}
     */
    load: function (Url, Target, afterUpdate) {

        if ($(Target).length === 0) {
            console.error('contentUpdater not found target.', Target);
            return true;
        }

        if ($(Target).length > 1) {
            console.error('contentUpdater found multi target', Target);
            return true;
        }

        console.warn("Target : " + Target.attr('id'));


        $(Target).trigger('beforeUpdate', Target);

        $(Target).load(Url + ' #' + Target.attr('id') + '>*', function (result, status) {

            if (status === 'error') {
                setTimeout(function () {
                    $(Target).trigger('endUpdate', Target);
                }, 2000);

                history.back();
                return false;
            }

            if (typeof afterUpdate === 'function') afterUpdate(result);

            console.log('Call End Update', Target);

            $(Target).trigger('endUpdate', Target);


        }.bind(this));
        return false;

    }
}

$(function () {
    contentUpdater.init();
});



