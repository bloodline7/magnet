<div class="row mt-3" id="withMenus">
    @php
        $sub = $sub ?? 'layouts';
    @endphp

    @includeIf("adminViews::".$sub.".left")

    <div class="content col-md col-lg pl-lg-0" >

        <div id="content">
            @yield('content')
        </div>

    </div>

    @includeIf("adminViews::layouts.right")
</div>
