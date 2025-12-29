{if $error}
    <div class="alert alert-danger" role="alert">
        {$error}
    </div>
{/if}

{if $note}
    <div class="alert alert-info" role="alert">
        {$note}
    </div>
{else}
    <div class="d-flex justify-content-center">
        <form method="get" action="index.php" class="form-inline">
            <input type="hidden" name="m" value="tmch">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Enter Lookup Key:</span>
                </div>
                <input type="text" name="lookupKey" id="lookupKey" class="form-control" required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
{/if}