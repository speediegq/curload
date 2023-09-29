<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";

if (!$enableAdminKeys || $enableAdminKeys == "false") {
    print "Admin keys are not supported.";
    die();
}

$Authorized = 0;
$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM admins');

$html .= "<!DOCTYPE html>\n";
$html .= "<html>\n";
$html .= "\t<head>\n";
$html .= "\t\t<meta name=\"description\" content=\"Site administration\">\n";
$html .= "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";
$html .= "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
$html .= "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
$html .= "\t\t<title>Administration - $instanceName</title>\n";
$html .= "\t</head>\n";
$html .= "\t<body>\n";
$html .= "\t\t<div class=\"content\">\n";

if (isset($_REQUEST['key'])) {
    $Key = $_REQUEST['key'];

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "") {
            $id = $line['id'];
            $lastUsed = date($dateFormat);

            $Database->exec("UPDATE admins SET lastused='$lastUsed' WHERE id='$id'");

            if ($storeIP || $storeIP == "true") {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                $Database->exec("UPDATE admins SET ip='$ip' WHERE id='$id'");
            }

            if ($storeAgent || $storeAgent == "true") {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $Database->exec("UPDATE admins SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            break;
        }
    }

    // the stuff
    if ($Authorized) {
        $html .= "\t\t\t<h2>Admin tools</h2>\n";
        $html .= "\t\t\t<iframe name=\"adminSubmit\" style=\"display: none;\"></iframe>\n";
        $html .= "\t\t\t<form action=\"create.php\" method=\"post\" target=\"adminSubmit\">\n";
        $html .= "\t\t\t\t<input type=\"text\" name=\"data\" placeholder=\"key name\">\n";
        $html .= "\t\t\t\t<input type=\"text\" name=\"type\" placeholder=\"type\">\n";
        $html .= "\t\t\t\t<input type=\"text\" name=\"uploads\" placeholder=\"max uploads\">\n";
        $html .= "\t\t\t\t<input type=\"hidden\" name=\"key\" value=\"$Key\">\n";
        $html .= "\t\t\t\t<input type=\"submit\" value=\"make\">\n";
        $html .= "\t\t\t</form>\n";
    } else {
        header('Location: admin.php?e=true');
        die();
    }
} else {
    $Authorized = 0;

    $html .= "\t\t\t<form action=\"admin.php\" method=\"post\">\n";
    $html .= "\t\t\t\t<input type=\"text\" name=\"key\" placeholder=\"Administrator key\">\n";
    $html .= "\t\t\t\t<input type=\"submit\" value=\"Login\">\n";
    $html .= "\t\t\t</form>\n";

    if (isset($_REQUEST['e']) && $_REQUEST['e'] == "true") {
        $html .= "\t\t\t<p>Invalid administrator key.</p>\n";
    }
}

$html .= "\t\t</div>\n";
$html .= "\t</body>\n";
$html .= "</html>\n";

print "$html";

?>
