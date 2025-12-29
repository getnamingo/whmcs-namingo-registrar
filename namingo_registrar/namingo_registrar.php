<?php
/**
 * Namingo Registrar for WHMCS (https://www.whmcs.com/)
 *
 * WHMCS module for Namingo Registrar implementing ICANN registrar technical requirements
 * Written in 2025 by Taras Kondratyuk (https://namingo.org)
 *
 * @license Apache-2.0
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

function namingo_registrar_config(): array
{
    return [
        'name'        => 'Namingo Registrar for WHMCS',
        'description' => 'WHMCS module for Namingo Registrar implementing ICANN registrar technical requirements',
        'author'      => 'Namingo',
        'version'     => '1.1.0',
        'fields' => [
            'whoisServer' => [
                'FriendlyName' => 'WHOIS Server',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'whois.example.com',
                'Description' => 'Enter the WHOIS server hostname',
            ],
            'rdapServer' => [
                'FriendlyName' => 'RDAP Server',
                'Type' => 'text',
                'Size' => '50',
                'Default' => 'rdap.example.com',
                'Description' => 'Enter the RDAP server hostname',
            ],
            'contactLink' => [
                'FriendlyName' => 'Contact Form Link',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '/index.php?m=namingo_registrar&page=contact&domain=',
                'Description' => 'Enter the URL for the contact form link',
            ],
            'whmcsApiKey' => [
                'FriendlyName' => 'WHMCS API Key',
                'Type' => 'text',
                'Size' => '50',
                'Description' => 'Enter the WHMCS API key for sending emails.',
            ],
            'tmch_username' => [
                'FriendlyName' => 'TMCH Username',
                'Type'         => 'text',
                'Size'         => '30',
                'Default'      => '',
                'Description'  => 'Username for TMCH authentication.',
            ],
            'tmch_password' => [
                'FriendlyName' => 'TMCH Password',
                'Type'         => 'password',
                'Size'         => '30',
                'Default'      => '',
                'Description'  => 'Password for TMCH authentication.',
            ],
            'tmch_test_server' => [
                'FriendlyName' => 'Use TMCH Test Server',
                'Type'         => 'yesno',
                'Default'      => '',
                'Description'  => 'Enable to use the TMCH test (OT&E) server instead of production.',
            ],
        ],
    ];
}

function namingo_registrar_activate(): array
{
    try {
        $sql = "

        -- Contact Table
        CREATE TABLE IF NOT EXISTS `namingo_contact` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `identifier` varchar(255) NOT NULL,
            `voice` varchar(17) default NULL,
            `fax` varchar(17) default NULL,
            `email` varchar(255) NOT NULL,
            `name` varchar(255) NOT NULL,
            `org` varchar(255) default NULL,
            `street1` varchar(255) default NULL,
            `street2` varchar(255) default NULL,
            `street3` varchar(255) default NULL,
            `city` varchar(255) NOT NULL,
            `sp` varchar(255) default NULL,
            `pc` varchar(16) default NULL,
            `cc` char(2) NOT NULL,
            `clid` int(10) unsigned NOT NULL,
            `crdate` datetime(3) NOT NULL,
            `upid` int(10) unsigned default NULL,
            `lastupdate` datetime(3) default NULL,
            `disclose_voice` enum('0','1') NOT NULL default '1',
            `disclose_fax` enum('0','1') NOT NULL default '1',
            `disclose_email` enum('0','1') NOT NULL default '1',
            `disclose_name_int` enum('0','1') NOT NULL default '1',
            `disclose_org_int` enum('0','1') NOT NULL default '1',
            `disclose_addr_int` enum('0','1') NOT NULL default '1',
            `nin` varchar(255) default NULL,
            `nin_type` enum('personal','business') default NULL,
            `validation` enum('0','1','2','3','4'),
            `validation_stamp` datetime(3) default NULL,
            `validation_log` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `identifier` (`identifier`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Domain Table
        CREATE TABLE IF NOT EXISTS `namingo_domain` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(68) NOT NULL,
            `registry_domain_id` varchar(68) NOT NULL,
            `registrant` int(10) unsigned default NULL,
            `admin` int(10) unsigned default NULL,
            `tech` int(10) unsigned default NULL,
            `billing` int(10) unsigned default NULL,
            `ns1` varchar(68) default NULL,
            `ns2` varchar(68) default NULL,
            `ns3` varchar(68) default NULL,
            `ns4` varchar(68) default NULL,
            `ns5` varchar(68) default NULL,
            `crdate` datetime(3) NOT NULL,
            `exdate` datetime(3) NOT NULL,
            `lastupdate` datetime(3) default NULL,
            `clid` int(10) unsigned NOT NULL,
            `crid` int(10) unsigned NOT NULL,
            `upid` int(10) unsigned default NULL,
            `trdate` datetime(3) default NULL,
            `trstatus` enum('clientApproved','clientCancelled','clientRejected','pending','serverApproved','serverCancelled') default NULL,
            `reid` int(10) unsigned default NULL,
            `redate` datetime(3) default NULL,
            `acid` int(10) unsigned default NULL,
            `acdate` datetime(3) default NULL,
            `transfer_exdate` datetime(3) default NULL,
            `idnlang` varchar(16) default NULL,
            `delTime` datetime(3) default NULL,
            `resTime` datetime(3) default NULL,
            `rgpstatus` enum('addPeriod','autoRenewPeriod','renewPeriod','transferPeriod','pendingDelete','pendingRestore','redemptionPeriod') default NULL,
            `rgppostData` text default NULL,
            `rgpdelTime` datetime(3) default NULL,
            `rgpresTime` datetime(3) default NULL,
            `addPeriod` tinyint(3) unsigned default NULL,
            `autoRenewPeriod` tinyint(3) unsigned default NULL,
            `renewPeriod` tinyint(3) unsigned default NULL,
            `transferPeriod` tinyint(3) unsigned default NULL,
            `renewedDate` datetime(3) default NULL,
            `agp_exempted` tinyint(1) DEFAULT 0,
            `agp_request` datetime(3) default NULL,
            `agp_grant` datetime(3) default NULL,
            `agp_reason` text default NULL,
            `agp_status` varchar(30) default NULL,
            `tm_notice_accepted` datetime(3) default NULL,
            `tm_notice_expires` datetime(3) default NULL,
            `tm_notice_id` varchar(150) default NULL,
            `tm_notice_validator` varchar(30) default NULL,
            `tm_smd_id` text default NULL,
            `tm_phase` VARCHAR(100) NOT NULL DEFAULT 'NONE',
            `phase_name` VARCHAR(75) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- Domain Status Table
        CREATE TABLE IF NOT EXISTS `namingo_domain_status` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `status` varchar(100) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `domain_status_unique` (`domain_id`, `status`),
            CONSTRAINT `domain_status_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        -- DNSSEC Table
        CREATE TABLE IF NOT EXISTS `namingo_domain_dnssec` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `domain_id` int(10) NOT NULL,
            `key_tag` int(11) NOT NULL,
            `algorithm` varchar(10) NOT NULL,
            `digest_type` varchar(10) NOT NULL,
            `digest` text NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `key_tag` (`key_tag`),
            CONSTRAINT `domain_dnssec_ibfk_1` FOREIGN KEY (`domain_id`) REFERENCES `tbldomains`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        Capsule::unprepared($sql);

        return [
            'status' => 'success',
            'description' => 'Module activated successfully.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Activation failed: ' . $e->getMessage(),
        ];
    }
}

function namingo_registrar_deactivate(): array
{
    try {
        Capsule::schema()->dropIfExists('namingo_contact');
        Capsule::schema()->dropIfExists('namingo_domain');
        Capsule::schema()->dropIfExists('namingo_domain_dnssec');
        Capsule::schema()->dropIfExists('namingo_domain_status');
        
        return [
            'status' => 'success',
            'description' => 'Module deactivated successfully.',
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Deactivation failed: ' . $e->getMessage(),
        ];
    }
}

/**
 * Client area entrypoint
 */
function namingo_registrar_clientarea(array $vars): array
{
    if (isset($_GET['action']) && $_GET['action'] === 'check') {
        $whoisServer = Capsule::table('tbladdonmodules')
            ->where('module', 'namingo_registrar')
            ->where('setting', 'whoisServer')
            ->value('value');

        $rdapServer = Capsule::table('tbladdonmodules')
            ->where('module', 'namingo_registrar')
            ->where('setting', 'rdapServer')
            ->value('value');

        $contactLink = Capsule::table('tbladdonmodules')
            ->where('module', 'namingo_registrar')
            ->where('setting', 'contactLink')
            ->value('value');

        // Prepare parameters for the handler function
        $params = array_merge($_POST, [
            'whoisServer' => $whoisServer,
            'rdapServer' => $rdapServer,
            'contactLink' => $contactLink,
        ]);

        $output = whois_check_handler($params);
        header('Content-Type: application/json');
        echo $output;
        exit;
    }

    $page = isset($_GET['page']) ? (string)$_GET['page'] : 'validation';

    // Basic allowlist so no one can request arbitrary template file names
    $routes = [
        'validation' => 'validation',
        'tmch'  => 'tmch',
        'whois'  => 'whois',
        'contact'  => 'contact',
    ];

    if (!isset($routes[$page])) {
        return [
            'pagetitle'    => 'Not found',
            'templatefile' => 'error',
            'requirelogin' => false,
            'vars'         => [
                'errorTitle' => 'Page not found',
                'errorMsg'   => 'Unknown page.',
            ],
        ];
    }

    if ($page === 'validation') {
        $message = '';
        $isError = false;
        $template = $vars['template'];

        $token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
        $token = preg_replace('/[^A-Za-z0-9_\-]/', '', $token);

        if ($token) {
            try {
                $client = Capsule::table('namingo_contact')
                    ->where('validation_log', $token)
                    ->first();

                if ($client && $client->validation == 0) {
                    $contact_id = $client->id;

                    Capsule::table('namingo_contact')
                        ->where('id', $contact_id)
                        ->update(['validation' => 1]);

                    $message = 'Contact information validated successfully!';
                } else {
                    $message = 'Error: Invalid or already validated validation token.';
                    $isError = true;
                }
            } catch (\Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $isError = true;
            }
        } else {
            $message = 'Please provide a validation token.';
            $isError = true;
        }

        return [
            'pagetitle' => 'Contact Validation',
            'breadcrumb' => ['index.php?m=namingo_registrar&page=validation' => 'Contact Validation'],
            'templatefile' => 'validation',
            'requirelogin' => false,
            'vars' => [
                'message' => $message,
                'isError' => $isError,
                'template' => $template,
            ],
        ];
    }

    if ($page === 'tmch') {
        $modulelink = $vars['modulelink'];
        $systemUrl = $vars['systemurl'];
        $tmchUser = $vars['tmch_username'] ?? '';
        $tmchPass = $vars['tmch_password'] ?? '';
        $useTest  = !empty($vars['tmch_test_server']);

        $lookupKey = isset($_GET['lookupKey']) ? trim((string)$_GET['lookupKey']) : '';
        $lookupKey = preg_replace('/[^A-Za-z0-9_\-]/', '', $lookupKey);

        if ($lookupKey) {
            $baseUrl = $useTest
                ? 'https://test.tmcnis.org/cnis/'
                : 'https://tmcnis.org/cnis/';

            $url = $baseUrl . $lookupKey . '.xml';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $xml = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
            }
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($xml) {
                $xml_object = simplexml_load_string($xml);
                $xml_object->registerXPathNamespace("tmNotice", "urn:ietf:params:xml:ns:tmNotice-1.0");
                $claims = $xml_object->xpath('//tmNotice:claim');

                $note = "This message is a notification that you have applied for a domain name that matches a trademark record submitted to the Trademark Clearinghouse. Your eligibility to register this domain name will depend on your intended use and if it is similar or relates to the trademarks listed below." . PHP_EOL;

                $note .= "Please be aware that your rights to register this domain name may not be protected as a noncommercial use or 'fair use' in accordance with the laws of your country. It is crucial that you read and understand the trademark information provided, including the trademarks, jurisdictions, and goods and services for which the trademarks are registered." . PHP_EOL;

                $note .= "It's also important to note that not all jurisdictions review trademark applications closely, so some of the trademark information may exist in a national or regional registry that does not conduct a thorough review of trademark rights prior to registration. If you have any questions, it's recommended that you consult with a legal expert or attorney on trademarks and intellectual property for guidance." . PHP_EOL;

                $note .= "By continuing with this registration, you're representing that you have received this notice and understand it and, to the best of your knowledge, your registration and use of the requested domain name will not infringe on the trademark rights listed below." . PHP_EOL;

                $note .= "The following " . count($claims) . " marks are listed in the Trademark Clearinghouse:" . PHP_EOL;

                $note .= PHP_EOL;

                foreach ($claims as $claim) {
                    $elements = $claim->xpath('.//*');
                    $firstHolder = true;
                    $firstContact = true;
                    foreach ($elements as $element) {
                        $elementName = trim($element->getName());
                        $elementText = trim((string) $element);
                        if (!empty($elementName) && !empty($elementText)) {
                            if ($element->xpath('..')[0]->getName() == "holder" && $firstHolder) {
                                $note .= "Trademark Registrant: " . PHP_EOL;
                                $firstHolder = false;
                            }
                            if ($element->xpath('..')[0]->getName() == "contact" && $firstContact) {
                                $note .= "Trademark Contact: " . PHP_EOL;
                                $firstContact = false;
                            }
                            $note .= $elementName . ": " . $elementText . PHP_EOL;
                        }
                    }
                    $note .= PHP_EOL;
                }

                return [
                    'pagetitle' => 'TMCH Claims Notice',
                    'breadcrumb' => ['index.php?m=namingo_registrar&page=tmch' => 'TMCH Claims Notice'],
                    'templatefile' => 'tmch',
                    'modulelink' => $modulelink,
                    'systemurl' => $systemUrl,
                    'requirelogin' => false,
                    'vars' => [
                        'note' => nl2br(htmlspecialchars($note)),
                    ],
                ];
            } else {
                $error = 'No claims notice loaded';
                return [
                    'pagetitle' => 'TMCH Claims Notice',
                    'breadcrumb' => ['index.php?m=namingo_registrar&page=tmch' => 'TMCH Claims Notice'],
                    'templatefile' => 'tmch',
                    'modulelink' => $modulelink,
                    'systemurl' => $systemUrl,
                    'requirelogin' => false,
                    'vars' => [
                        'error' => htmlspecialchars($error),
                    ],
                ];
            }
        } else {
            return [
                'pagetitle' => 'TMCH Claims Notice',
                'breadcrumb' => ['index.php?m=namingo_registrar&page=tmch' => 'TMCH Claims Notice'],
                'templatefile' => 'tmch',
                'requirelogin' => false,
            ];
        }
    }

    if ($page === 'whois') {
        $modulelink = $vars['modulelink'];
        $systemUrl = $vars['systemurl'];
        $whoisServer = $vars['whoisServer'];
        $rdapServer = $vars['rdapServer'];
        $contactLink = $vars['contactLink'];

        return [
            'pagetitle' => 'Domain Lookup',
            'breadcrumb' => ['index.php?m=whois' => 'Domain Lookup'],
            'templatefile' => 'whois',
            'requirelogin' => false,
            'vars' => [
                'modulelink' => $modulelink,
                'systemurl' => $systemUrl,
                'whoisServer' => $whoisServer,
                'rdapServer' => $rdapServer,
                'contactLink' => $contactLink,
            ],
        ];
    }

    if ($page === 'contact') {
        $modulelink = $vars['modulelink'];
        $systemUrl = $vars['systemurl'];
        $apiKey = $vars['whmcsApiKey'];
        $domain = $_GET['domain'] ?? null;

        $success = null;
        $error = null;

        if ($domain) {
            $domainExists = Capsule::table('tbldomains')->where('domain', $domain)->first();
            
            if (!$domainExists) {
                $error = "Error: The specified domain does not exist.";
            }
        } else {
            $error = "Error: You must specify a domain.";
        }

        if (!$error && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $submissionResult = contact_handleSubmission($domain, $apiKey);
            if ($submissionResult === true) {
                $success = "Your message has been sent successfully!";
            } else {
                $error = $submissionResult;
            }
        }

        return [
            'pagetitle' => 'Domain Registrant Contact',
            'breadcrumb' => ['index.php?m=contact' => 'Domain Registrant Contact'],
            'templatefile' => 'contact',
            'requirelogin' => false,
            'vars' => [
                'modulelink' => $modulelink,
                'systemurl' => $systemUrl,
                'domain' => $domain,
                'success' => $success,
                'error' => $error,
            ],
        ];
    }

    // fallback
    return [
        'pagetitle'    => 'Error',
        'templatefile' => 'error',
        'requirelogin' => false,
        'vars'         => [
            'errorTitle' => 'Error',
            'errorMsg'   => 'Unhandled route.',
        ],
    ];
}

function whois_check_handler($params)
{
    $whoisServer = $params['whoisServer'];
    $rdapServer = 'https://' . $params['rdapServer'] . '/domain/';

    $domain = $_POST['domain'];
    $type = $_POST['type'];

    $sanitized_domain = filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);

    if (!$sanitized_domain) {
        return json_encode(['error' => 'Invalid domain.']);
    }

    if ($type === 'whois') {
        $output = '';
        $socket = fsockopen($whoisServer, 43, $errno, $errstr, 30);

        if (!$socket) {
            return json_encode(['error' => "Error fetching WHOIS data."]);
        }
        
        fwrite($socket, $domain . "\r\n");
        while (!feof($socket)) {
            $output .= fgets($socket);
        }
        fclose($socket);
    } elseif ($type === 'rdap') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rdapServer . $domain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
        }

        curl_close($ch);

        if (!$output) {
            return json_encode(['error' => 'Error fetching RDAP data.']);
        }
    }
    return $output;
}

function contact_handleSubmission($domain, $apiKey)
{
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // Check if the domain exists in WHMCS
    $domainExists = Capsule::table('tbldomains')->where('domain', $domain)->first();

    if (!$domainExists) {
        return "Error: The specified domain does not exist.";
    }

    // Send email via WHMCS API
    $userId = $domainExists->userid;
    $systemUrl = \WHMCS\Config\Setting::getValue('SystemURL');
    $apiUrl = $systemUrl . '/includes/api.php';

    $postfields = [
        'action' => 'SendEmail',
        'apikey' => $apiKey,
        'messagename' => 'Registrant Contact Message',
        'id' => $userId,
        'customsubject' => 'Contact Form Submission for Domain: ' . $domain,
        'custommessage' => "Name: $name\nEmail: $email\nMessage:\n$message",
    ];

    $queryString = http_build_query($postfields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse['result']) && $decodedResponse['result'] === 'success') {
            return true;
        }
        return $decodedResponse['message'] ?? 'Error: Unable to send the message.';
    }

    return 'Error: Unable to connect to WHMCS API.';
}