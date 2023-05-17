<?php

return [
    'title' => (env('SERVICE') == "show") ? "GCExpo Admin" : 'AusumSports',
    'title_sub' => '<span style="font-size: 7px; color:#ea97cd">Utillity</span>',
    'prefix' => (env('SERVICE') == "show") ? "gceadmin" : 'admin',
    'show_prefix' => 'gceadmin',
    'controller_base' => '\Ausumsports\Admin\Http\\'
];
