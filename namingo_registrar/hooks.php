<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

add_hook('ClientAreaPrimaryNavbar', 1, function ($navbar) {
    $navbar->addChild('Domain Lookup', [
        'label' => 'Domain Lookup',
        'uri'   => 'lookup',
        'order' => 50,
    ]);
    
    if (!empty($navbar->getChild('Services'))) {
        $services = $navbar->getChild('Services');

        $services->addChild('namingoClaimsNotice', [
            'label' => 'Claims Notice',
            'uri'   => '/claims',
            'order' => 999,
        ]);
    }
});