<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Authorized = 0;
$KeyType = 0;
$Redirect = "";

if (isset($_REQUEST['redir'])) {
    $Redirect = $_REQUEST['redir'];
}

// if a cookie exists, redirect the user there instead
if (isset($_COOKIE[$cookieName])) {
    if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == "true") {
        setcookie($cookieName, "", 0);
        setcookie($cookieTypeName, "", 0);
        header('Location: login.php');
        die();
    }

    if ($Redirect == "index" || ($Redirect == "admin" && $_COOKIE[$cookieTypeName] != 2) || $Redirect == "") {
        header('Location: /');
        die();
    } else if ($Redirect == "admin") {
        header('Location: admin.php');
        die();
    }
}

if (isset($_REQUEST['key'])) {
    $Key = $_REQUEST['key'];

    // check the validity of the key
    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM keys');

    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && ($enableKeys || $enableKeys == "true")) {
            $id = $line['id'];

            // update last usage
            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE keys SET lastused='$lastUsed' WHERE id='$id'");
            }

            // update IP address
            if ($storeIP || $storeIP == "true") {
                $ip = getIPAddress();
                $Database->exec("UPDATE keys SET ip='$ip' WHERE id='$id'");
            }

            // update user agent
            if ($storeAgent || $storeAgent == "true") {
                $userAgent = getUserAgent();
                $Database->exec("UPDATE keys SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $KeyType = $line['keytype'];

            break;
        }
    }

    if ($Authorized != 1) {
        if ($Redirect != "") { // just so we can try again and still be redirected to the right place
            header("Location: login.php?e=true&redir=$Redirect");
        } else {
            header("Location: login.php?e=true");
        }
        die();
    }

    setcookie($cookieName, $Key);
    setcookie($cookieTypeName, $KeyType);

    if ($Redirect != "") { // just so we can try again and still be redirected to the right place
        header("Location: login.php?e=true&redir=$Redirect");
    } else {
        header("Location: login.php?e=true");
    }

    die();
} else {
    $html = "";

    $html = printHeader($html);

    $html .= "\t\t\t<h1 id='loginHeader'>Login</h1>\n";
    $html .= "\t\t\t\t<p>Enter your login key to continue.</p>\n";
    $html .= "\t\t\t\t<form action=\"login.php\">\n";
    $html .= "\t\t\t\t\t<input type=\"password\" name=\"key\" placeholder=\"Login key\">\n";
    if (isset($Redirect)) $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"redir\" value=\"$Redirect\">\n";
    $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Login\">\n";
    $html .= "\t\t\t\t</form>\n";

    if (isset($_REQUEST['e']) && $_REQUEST['e'] == "true") {
        $html .= "\t\t\t\t<p class=\"error\">Invalid key.</p>\n";
    }

    $html = printFooter($html);

    print "$html";
}
?>
