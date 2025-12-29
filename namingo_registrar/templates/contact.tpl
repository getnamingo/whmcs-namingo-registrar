<div class="col-md-12">
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">Contact Domain Registrant</h3>
            
            {if $success}
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-fw"></i> {$success}
                </div>
            {/if}
            
            {if $error}
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle fa-fw"></i> {$error}
                </div>
            {/if}

            {if $domain && !$success}
            <form method="POST" action="{$modulelink}&domain={$domain|escape}">

                <h3 class="card-title">Domain: {$domain|escape}</h3>

                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label">Your Name:</label>
                    <div class="col-md-6">
                        <input type="text" name="name" id="name" class="form-control" required />
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label">Your Email:</label>
                    <div class="col-md-6">
                        <input type="email" name="email" id="email" class="form-control" required />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="message" class="col-md-4 col-form-label">Your Message:</label>
                    <div class="col-md-6">
                        <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        Send Message
                    </button>
                </div>

            </form>
            {/if}    
        </div>
    </div>
</div>