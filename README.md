# Namingo Registrar for WHMCS
WHMCS module for Namingo Registrar implementing ICANN registrar technical requirements

## Installation

```bash
git clone https://github.com/getnamingo/whmcs-namingo-registrar
mv whmcs-namingo-registrar/namingo_registrar /var/www/html/whmcs/modules/addons
chown -R www-data:www-data /var/www/html/whmcs/modules/addons/namingo_registrar
chmod -R 755 /var/www/html/whmcs/modules/addons/namingo_registrar
```

- Go to Settings > Apps & Integrations in the admin panel, search for "Namingo Registrar" and then activate it.

## Upgrading from `whmcs_registrar` module (v1.0 or earlier)

- **Back up your WHMCS database** (and optionally the full WHMCS directory).

- **Deactivate the following addon modules** (make sure to **copy/save their current settings** first):
  - Domain Contact Verification  
  - TMCH Claims Notice Support  
  - WHOIS & RDAP Client  
  - Domain Registrant Contact

- Edit:
  `/var/www/html/whmcs/modules/addons/whmcs_registrar/whmcs_registrar.php`  
  In the `_deactivate()` function, **remove** the following lines (to prevent dropping Namingo tables):
  ```php
  Capsule::schema()->dropIfExists('namingo_contact');
  Capsule::schema()->dropIfExists('namingo_domain');
  Capsule::schema()->dropIfExists('namingo_domain_dnssec');
  Capsule::schema()->dropIfExists('namingo_domain_status');
  ```
  
- **Deactivate** the **ICANN Registrar** module.

- Install the new module (as described in the installation section), but **do not activate it yet**.

- Edit `/var/www/html/whmcs/modules/addons/namingo_registrar/namingo_registrar.php`  
  In the `_activate()` function, **comment out** the line `Capsule::unprepared($sql);`.
  
- Now **activate the new module** and **reconfigure it** using the settings you saved from the old modules.

## Usage Instructions

This module is a **WHMCS module for Namingo Registrar implementing ICANN registrar technical requirements**.  
It is an integral part of the **Namingo Registrar** project and is intended to be used as the WHMCS integration layer for registrar operations.

Detailed, step-by-step usage instructions are provided as part of the Namingo Registrar documentation and project resources.

For the Namingo Registrar core project and overall architecture, see:  
https://github.com/getnamingo/registrar

## Support

Your feedback and inquiries are invaluable to Namingo's evolutionary journey. If you need support, have questions, or want to contribute your thoughts:

- **Email**: Feel free to reach out directly at [help@namingo.org](mailto:help@namingo.org).

- **Discord**: Or chat with us on our [Discord](https://discord.gg/97R9VCrWgc) channel.
  
- **GitHub Issues**: For bug reports or feature requests, please use the [Issues](https://github.com/getnamingo/whmcs-namingo-registrar/issues) section of our GitHub repository.

We appreciate your involvement and patience as Namingo continues to grow and adapt.

## 💖 Support This Project

If you find Namingo Registrar for WHMCS useful, consider donating:

- [Donate via Stripe](https://donate.stripe.com/7sI2aI4jV3Offn28ww)
- BTC: `bc1q9jhxjlnzv0x4wzxfp8xzc6w289ewggtds54uqa`
- ETH: `0x330c1b148368EE4B8756B176f1766d52132f0Ea8`

## Licensing

Namingo Registrar for WHMCS is licensed under the Apache License 2.0.