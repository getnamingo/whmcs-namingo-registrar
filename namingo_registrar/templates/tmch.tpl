<h1 class="mb-4">TMCH Claims Notice</h1>
<div class="card mb-4">
    <div class="card-body">
    {if $error}
        <div class="alert alert-danger" role="alert">
        {$error}
        </div>
    {/if}

    {if $note}
        <div class="alert alert-info" role="alert">
        {$note}
        </div>
    {/if}

        <form method="get" action="{$modulelink}">
            <input type="hidden" name="m" value="namingo_registrar">
            <input type="hidden" name="page" value="tmch">
            <div class="row mb-3">
                <div class="col-12 col-md-8">
                    <input type="text" class="form-control form-control-lg" placeholder="Enter Lookup Key" autocapitalize="none" name="lookupKey" id="lookupKey" required>
                </div>
                <div class="col-12 col-md-4 mt-3 mt-md-0 text-md-end">
                    <button type="submit" class="btn btn-info btn-lg w-100 w-md-auto">Display Notice</button>
                </div>
            </div>
        </form>
    </div>
</div>