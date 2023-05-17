
import Echo from "laravel-echo";
window.io = require('socket.io-client');
window.socketConnect = function (Host)
{
    Host = Host || 'https://ws.ausumsports.com'

    window.Echo = new Echo({
        broadcaster: 'socket.io',
        connector: 'socket.io',
        host: Host,
        authEndpoint: '/admin/auth',
        transports: ['websocket'],
        encrypted: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    console.warn("Socket Ready for " + Host);

    $(window).trigger("socketReady");


}
