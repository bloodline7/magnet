<header data-user="{{ auth()->user()->id ?? "" }}">


    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/{{ config('admin.prefix') }}">{{ config('admin.title') }}
                <sub>{!! config('admin.title_sub') !!}</sub></a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01"
                aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav me-auto">


                    <li class="nav-item">
                        <a class="nav-link" href="/{{ config('admin.prefix') }}/collection">
                            <i class="fas fa-boxes"></i>Data Collection</a>
                    </li>

<!--

                    <li class="nav-item">
                        <a class="nav-link" href="/{{ config('admin.prefix') }}/convention"><i class="fas fa-person-booth"></i> Convention</a>
                    </li>
-->

                    {{-- <li class="nav-item">
                         <a class="nav-link" href="/{{ config('admin.prefix') }}/board"><i class="fas fa-chalkboard-teacher"></i>
                             Board</a>
                     </li>
 --}}

                    {{--

                       <li class="nav-item">
                           <a class="nav-link" href="/{{ config('admin.prefix') }}/system/router">Router</a>
                       </li>--}}

                    <li class="nav-item">
                        <a class="nav-link" href="/{{ config('admin.prefix') }}/system"><i
                                class="fas fa-tools"></i> System</a>
                    </li>


                    {{--<li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                           aria-haspopup="true" aria-expanded="false">System</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/{{ config('admin.prefix') }}/system/router">Router</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Separated link</a>
                        </div>
                    </li>--}}
                </ul>

                @if (auth()->user())
                    <div class="d-flex">
                        <ul class="navbar-nav me-auto">

                            <li class="nav-item me-2 mt-1">
                                <i class="fas fa-user-shield"></i> {{ auth()->user()->name  }}
                            </li>

                            <li class="nav-item me-2">
                                <a class="nav-link btn" id="signout"><i class="fas fa-sign-out-alt"></i> Sign out</a>
                            </li>

                            <li class="nav-item mt-1">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="consoleBt" checked=""> <label
                                        for="consoleBt">Console</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                @endif


            </div>

        </div>
    </nav>

</header>
