{if $message}
    {if $isError}
        <div class="alert alert-warning">{$message|escape}</div>
    {else}
        <div class="alert alert-success">{$message|escape}</div>
    {/if}
{else}
    <div class="alert alert-warning">No validation token provided in the URL.</div>
{/if}