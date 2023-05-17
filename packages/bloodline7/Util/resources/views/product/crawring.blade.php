@extends('adminViews::layouts.master')

@section('content')

    <div class="card border-secondary mb-3">
        <div class="card-header">Crawling</div>
        <div class="card-body">
            <h4 class="card-title">Search web site</h4>
            <p class="card-text">

            <form name="searchSite" id="searchSite" method="post">
                <fieldset class="searchSite">
                    <legend class="mt-1">Check for Search</legend>

                    <div class="form-check form-switch">
                        <input class="form-check-input" id="dacardworld" value="dacardworld" type="checkbox" checked="">
                        <label class="form-check-label" for="dacardworld">
                            https://www.dacardworld.com/
                        </label>
                    </div>

                    {{-- <div class="form-check form-switch">
                         <input class="form-check-input" type="checkbox" id="blowoutcards" value="blowoutcards" checked="">
                         <label class="form-check-label" for="blowoutcards">
                             https://www.blowoutcards.com/
                         </label>
                     </div>--}}

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="steelcitycollectibles"
                               value="steelcitycollectibles" checked="">
                        <label class="form-check-label" for="steelcitycollectibles">
                            https://www.steelcitycollectibles.com/
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="chicagolandsportscards"
                               value="chicagolandsportscards" checked="">
                        <label class="form-check-label" for="chicagolandsportscards">
                            https://www.chicagolandsportscards.com/
                        </label>
                    </div>

                </fieldset>


                <fieldset class="searchOption">

                    <legend class="mt-2">Search Options</legend>

                    <div class="form-check ">
                        <input class="form-check-input" checked name="scrollbar" value="Y" type="checkbox" id="scrollbar">
                        <label class="form-check-label" for="scrollbar">Result Box Scrollbar</label>
                    </div>


                    <div class="input-group mt-2" role="group">
                        <input type="text" class="form-control" name="keyword" placeholder="Search Keyword"
                               aria-label="search keyword">
                        <div class="input-group-append">
                            <input type="submit" class="btn bg-secondary" value="Search">
                        </div>
                    </div>

                </fieldset>
            </form>

            </p>
        </div>
    </div>

    <div class="searchResult">

        <div class="card border-success mb-3" id="result_dacardworld">
            <div class="card-header">https://www.dacardworld.com</div>
            <div class="card-body">
                <h4 class="card-title">Search results for '<span class="keyword"></span>' <span class="rows">0</span> item found.</h4>
                <div class="card-text row justify-content-around"></div>
            </div>
        </div>


        <div class="card border-danger mb-3" id="result_blowoutcards">
            <div class="card-header">https://www.blowoutcards.com</div>
            <div class="card-body">
                <h4 class="card-title">Search results for '<span class="keyword"></span>'   <span class="rows">0</span> item found.</h4>
                <div class="card-text row justify-content-around"></div>
            </div>
        </div>


        <div class="card border-warning mb-3" id="result_steelcitycollectibles">
            <div class="card-header">https://www.steelcitycollectibles.com</div>
            <div class="card-body">
                <h4 class="card-title">Search results for '<span class="keyword"></span>' <span class="rows">0</span> item found.</h4>
                <div class="card-text row justify-content-around"></div>
            </div>
        </div>


        <div class="card border-info mb-3" id="result_chicagolandsportscards">
            <div class="card-header">https://www.chicagolandsportscards.com/</div>
            <div class="card-body">
                <h4 class="card-title">Search results for '<span class="keyword"></span>' <span class="rows">0</span> item found.</h4>
                <div class="card-text row justify-content-around"></div>
            </div>
        </div>

        {{--
                <div class="card border-light mb-3">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h4 class="card-title">Light card title</h4>
                        <div class="card-text">Some quick example text to build on the card title and make up the bulk of the
                            card's content.</div>
                    </div>
                </div>

                <div class="card border-dark mb-3">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h4 class="card-title">Dark card title</h4>
                        <div class="card-text">Some quick example text to build on the card title and make up the bulk of the
                            card's content.</div>
                    </div>
                </div>--}}

    </div>

    <div class="modal fade ">
        <div class="modal-dialog modal-dialog-centered mw-80" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="false"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
