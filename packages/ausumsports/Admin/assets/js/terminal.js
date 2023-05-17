
import {Terminal} from 'xterm';
import {FitAddon} from 'xterm-addon-fit';

const c = require('ansi-colors');


const terminal = {

    init: function () {

        if (!$("#console-screen").length) return;

        console.log("ready to console screen");

        this.term = this.getTerminal();
        this.term.open(document.getElementById('console-screen'));
        this.fitAddon = this.getFitAddon();
        this.fitAddon.fit();
        $(window).on('socketReady', function () {

            console.log("socketReady");


            this.connect();
        }.bind(this));

        this.setOptionBt(false);
        this.setInput();

    },

    setInput : function ()
    {
        $(".xterm-cursor-layer").remove();

        $("#console-input").find("input:first").keypress(function (e) {

            console.log(e.keyCode);

            if(e.keyCode == 13)
            {
                const value = $("#console-input").find("input:first").val();

                $.post("/"+window.prefix+"/message", { msg : value });

                //this.term.writeln(c.white(value));

                $("#console-input").find("input:first").val('')

            }
        }.bind(this));


    },

    setOptionBt : function (toggle)
    {
        const status =  toggle;
        $("#consoleBt").prop('checked', status);

         const checkOption = function (Bt)
         {
            if($(Bt).prop('checked')) {

                $("#console").css('top', '60%');
                terminal.fitAddon.fit();

                $("body:first").addClass('terminal');
                $("#console-input").show().find('input:first').focus();


            }
            else {
                $("#console").css('top', '100%');
                $("body:first").removeClass('terminal');
                $("#console-input").hide();

            }
        }

        checkOption($("#consoleBt"));

        $("#consoleBt").click( function () { checkOption(this); });

    },

    connect: function () {
        const term = this.term;

        window.Echo.connector.socket
            .on('connect', () => {
                term.writeln(c.cyan('Socket Connected..'));
                term.writeln(c.yellow('Listening Logs...' + "\n\n"));
            })
            .on('disconnect', () => {
            term.writeln(c.cyan('Socket DisConnected..'));
        });

        window.Echo.channel('admin')
            .listen('.log', (data) => {
                term.write(data.log);
            });

        const userID = $("#console").attr('data-user');

        if(userID)
        {
            window.Echo.channel('private-personal.'+ userID)
                .listen('.personal', (data) => {
                    term.writeln(data.message);
                });
        }

    },

    getFitAddon: function () {
        const fitAddon = new FitAddon();
        this.term.loadAddon(fitAddon);
        return fitAddon;
    }
    ,

    getTerminal: function () {

        const term = new Terminal({
            theme: {
                background: 'rgba(55, 55, 55, 0.1)'
            },
            fontSize: 14,
            fontFamily: 'Ubuntu Mono, courier-new, courier, monospace',
            letterSpacing: 0,
            allowTransparency: true,
            cursorWidth: 1,
            cursorBlink: false,
            visualBell: true,
            windowsMode: true,
            convertEol: true
        });

        return term;
    }
}

$(function () {
    terminal.init();
})
