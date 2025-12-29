<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    $html = <<<HTML
<div class="text-center" style="margin:10px 0;">
  <p><a href="index.php?m=namingo_registrar&page=whois">Domain Lookup</a>
  &nbsp;|&nbsp;
  <a href="index.php?m=namingo_registrar&page=tmch">TMCH Claims Notice</a></p>
</div>
HTML;
    return $html;
});

add_hook('ClientAreaPrimaryNavbar', 1, function ($navbar) {
    $navbar->addChild('Domain Lookup', [
        'label' => 'Domain Lookup',
        'uri'   => 'index.php?m=namingo_registrar&page=whois',
        'order' => 50,
    ]);
});