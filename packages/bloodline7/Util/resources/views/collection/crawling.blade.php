@extends('adminViews::layouts.master')

@section('content')

    <div class="card border-secondary mb-3">
        <div class="card-header">Crawling</div>
        <div class="card-body">
            <h4 class="card-title">Search web site</h4>
            <p class="card-text">

            <form name="searchSite" id="searchSite" method="post">
                <fieldset class="searchSite">
                    <legend class="mt-1">Check for Crawling</legend>

                    <br>

                    <br>


                    <div class="form-check form-switch">
                        <input class="form-check-input" id="ebayCardList" value="ebayCardList" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="ebayCardList">
                            ebay CardList
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" id="ebayBoxList" value="ebayBoxList" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="ebayBoxList">
                            ebay BoxList
                        </label>
                    </div>


                    <div class="form-check form-switch">
                        <input class="form-check-input" id="ebayCaseList" value="ebayCaseList" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="ebayCaseList">
                            ebay CaseList
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" id="ebayProducts" value="ebayProducts" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="ebayProducts">
                            ebay Products
                        </label>
                    </div>

                    <hr>


                    <div class="form-check form-switch">
                        <input class="form-check-input" id="scc" value="scc" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="scc">
                            Sports Card Checklist .com
                        </label>
                    </div>



                    <br />
                    <br /><br />

                    <div class="form-check form-switch">
                        <input class="form-check-input" id="Brands" value="Brands" name="crType[]" type="checkbox">
                        <label class="form-check-label" for="Brands">
                          Brands
                        </label>
                    </div>


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="Product" name="crType[]"
                                value="Product" >
                        <label class="form-check-label" for="Product">
                            Products
                        </label>
                    </div>


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ProductNew" name="crType[]"
                                value="ProductNew" >
                        <label class="form-check-label" for="ProductNew">
                            ProductNew - List Crawling
                        </label>
                    </div>


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ProductNew2" name="crType[]"
                                value="ProductNew-brand" >
                        <label class="form-check-label" for="ProductNew2">
                            ProductNew - Brand Crawling
                        </label>
                    </div>


                    <br />
                    <br />


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ProductSetSports" name="crType[]"
                                value="ProductSetSports" >
                        <label class="form-check-label" for="ProductSetSports">
                            Product Set Sports
                        </label>
                    </div>



                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ProductDetail" name="crType[]"
                                value="ProductDetail" >
                        <label class="form-check-label" for="ProductDetail">
                            Product Detail Collect
                        </label>
                    </div>

                    <br />


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="checkListMenu" name="crType[]"
                                value="checkListMenu" >
                        <label class="form-check-label" for="checkListMenu">
                            CheckList Menu
                        </label>
                    </div>


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="checkListCard" name="crType[]"
                                value="checkListCard" >
                        <label class="form-check-label" for="checkListCard">
                            CheckList Player Card Set
                        </label>
                    </div>


                    <br />


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="Player" name="crType[]"
                                value="Player">
                        <label class="form-check-label" for="Player">
                            Players
                        </label>
                    </div>


                    <div class="input-group mt-2" role="group">
<!--                        <input type="text" class="form-control" name="keyword" placeholder="Search Keyword"
                                aria-label="search keyword">
                        -->

                        <div class="input-group">
                            <input type="submit" class="btn bg-secondary" value="Start Crowling">
                        </div>
                    </div>

                </fieldset>


<!--                <fieldset class="searchOption">

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

                </fieldset>-->
            </form>
    </div>
    </div>

@endsection
