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
});

add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    // Only on checkout page
    if (($vars['filename'] ?? '') !== 'cart') return '';
    if (($_GET['a'] ?? '') !== 'checkout') return '';

    $claims = $_SESSION['namingo_tmch_claims'] ?? [];
    if (empty($claims)) return '';

    $systemUrl = rtrim($vars['systemurl'] ?? '', '/') . '/';

    $items = '';
    foreach ($claims as $domainKey => $data) {
        $lookupKey = (string)($data['lookupKey'] ?? '');
        if ($lookupKey === '') continue;

        $domain = (string)$domainKey;
        $url = $systemUrl . 'claims?m=namingo_registrar&page=tmch&lookupKey=' . rawurlencode($lookupKey);

        $items .= '<li><a href="' . htmlspecialchars($url, ENT_QUOTES) . '" target="_blank" rel="noopener">View TMCH Claims Notice</a> for <strong>'
            . htmlspecialchars($domain, ENT_QUOTES) . '</strong></li>';
    }

    if ($items === '') return '';

    // Visible block
return '
<div id="namingo-tmch-claims" class="alert alert-warning" style="display:none;">
  <div style="font-weight:600; margin-bottom:6px;">Trademark Claims Notice required</div>
  <ul style="margin:0 0 10px 18px;">' . $items . '</ul>

  <label style="display:flex; gap:8px; align-items:flex-start; margin:0;">
    <input type="checkbox" id="namingo_tmch_accept_cb" />
    <span>I have reviewed and accept the TMCH Claims Notice(s) above.</span>
  </label>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  var box = document.getElementById("namingo-tmch-claims");
  if (!box) return;

  // Find checkout form
  var form = document.getElementById("frmCheckout")
          || document.querySelector("form[action*=\"cart.php\"][method=\"post\"]")
          || document.querySelector("form");
  if (!form) return;

  // Find the Complete Order button (Twenty-One)
  var completeBtn = document.getElementById("btnCompleteOrder")
                || document.querySelector("button[type=\"submit\"], input[type=\"submit\"]");
  if (!completeBtn) return;

  // Insert box right BEFORE the Complete Order button
  var target = completeBtn.closest(".text-center, .pull-right, .form-group, .row") || completeBtn.parentElement;
  target.parentNode.insertBefore(box, target);

  // Show now that it is placed nicely
  box.style.display = "";

  // Hidden input for server-side validation
  var hidden = document.getElementById("namingo_tmch_accept");
  if (!hidden) {
    hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "namingo_tmch_accept";
    hidden.id = "namingo_tmch_accept";
    hidden.value = "";
    form.appendChild(hidden);
  }

  var cb = document.getElementById("namingo_tmch_accept_cb");
  if (cb) {
    cb.addEventListener("change", function () {
      hidden.value = cb.checked ? "1" : "";
    });

    // Nice UX: prevent submit if not checked
    form.addEventListener("submit", function (e) {
      if (!cb.checked) {
        e.preventDefault();
        alert("You must review and accept the TMCH Claims Notice(s) before completing your order.");
      }
    });
  }
});
</script>
';
});

add_hook('ShoppingCartValidateCheckout', 1, function ($vars) {
    $claims = $_SESSION['namingo_tmch_claims'] ?? [];
    if (empty($claims)) return;

    $accepted = !empty($_SESSION['namingo_tmch_accepted_all'])
        || (!empty($_REQUEST['namingo_tmch_accept']) && $_REQUEST['namingo_tmch_accept'] === '1');

    if (!$accepted) {
        return ["You must review and accept the TMCH Claims Notice before checkout."];
    }

    $_SESSION['namingo_tmch_accepted_all'] = true;
});