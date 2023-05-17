@extends('adminViews::layouts.master')

@section('content')
<style>
    form small {

        color: #5d3462;
        text-shadow: 1px 1px #8d8787;
    }
</style>

    <div class="card border-secondary mb-3">
        <div class="card-header">Importing</div>
        <div class="card-body">
            <h4 class="card-title">Import from MongoDB to MySQL</h4>
            <p class="card-text">

            <form name="searchSite" id="searchSite" method="post">
                <fieldset class="searchSite">

                    <legend class="mt-1">Check for Importing</legend>


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="cleanUpCheckList" name="crType[]"
                                value="cleanUpCheckList">
                        <label class="form-check-label" for="cleanUpCheckList">
                            CleanUp CheckList<br>
                            <small>CleanUp CheckList Model</small>
                        </label>
                    </div>


                    <br />




                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="cleanUpEbay" name="crType[]"
                                value="cleanUpEbay">
                        <label class="form-check-label" for="cleanUpEbay">
                            CleanUp Ebay Items<br>
                            <small>CleanUp by Ebay Items</small>
                        </label>
                    </div>


                    <br />

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ebayFileImporting" name="crType[]"
                                value="ebayFileImporting">
                        <label class="form-check-label" for="ebayFileImporting">
                            eBay File Importing <br>
                            <small>Products File Importing by Ebay Products</small>
                        </label>
                    </div>
                    <br />


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ebayProducts" name="crType[]"
                                value="ebayProducts">
                        <label class="form-check-label" for="ebayProducts">
                           eBay Products <br>
                            <small>Products Importing by Ebay Products</small>
                        </label>
                    </div>


                    <br />
                    <br />


                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="itemAnalysis" name="crType[]"
                                value="itemAnalysis">
                        <label class="form-check-label" for="itemAnalysis">
                            Items Analysis <br>
                            <small>Analysis and Importing by Ebay Items</small>
                        </label>
                    </div>




                    <br />    <br />

                    <div class="input-group mt-2" role="group">
                        <div class="input-group">
                            <input type="submit" class="btn bg-secondary" value="Start Importing">
                        </div>
                    </div>

                </fieldset>

            </form>
    </div>
    </div>

@endsection
