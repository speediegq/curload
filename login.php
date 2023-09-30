<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "create-table.php";
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
        header('Location: login.php');
        die();
    }

    if ($Redirect == "index" || ($Redirect == "admin" && $KeyType != 3) || $Redirect == "") {
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

    // Regular keys
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
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                $Database->exec("UPDATE keys SET ip='$ip' WHERE id='$id'");
            }

            // update user agent
            if ($storeAgent || $storeAgent == "true") {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $Database->exec("UPDATE keys SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $KeyType = 0;

            break;
        }
    }

    // Temporary keys
    $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && ($enableTemporaryKeys || $enableTemporaryKeys == "true")) {
            $id = $line['id'];

            // update last usage
            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE tkeys SET lastused='$lastUsed' WHERE id='$id'");
            }

            // update IP address
            if ($storeIP || $storeIP == "true") {
                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                $Database->exec("UPDATE tkeys SET ip='$ip' WHERE id='$id'");
            }

            // update user agent
            if ($storeAgent || $storeAgent == "true") {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $Database->exec("UPDATE tkeys SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $KeyType = 1;

            break;
        }
    }

    // Admin keys
    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['key'] == $Key && $Key != "" && $line['key'] != "" && ($enableTemporaryKeys || $enableTemporaryKeys == "true")) {
            $id = $line['id'];

            // update last usage
            if ($storeLastUsage || $storeLastUsage == "true") {
                $lastUsed = date($dateFormat);
                $Database->exec("UPDATE admins SET lastused='$lastUsed' WHERE id='$id'");
            }

            // update IP address
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

            // update user agent
            if ($storeAgent || $storeAgent == "true") {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                $Database->exec("UPDATE admins SET useragent='$userAgent' WHERE id='$id'");
            }

            $Authorized = 1;
            $KeyType = 2;

            break;
        }
    }

    if ($Authorized == 0) {
        header('Location: login.php?e=true');
        die();
    }

    setcookie($cookieName, $Key);

    if (!isset($_COOKIE[$cookieName])) {
        header('Location: /');
        die();
    }

    if ($Redirect == "index" || ($Redirect == "admin" && $KeyType != 3)) {
        header('Location: /');
        die();
    } else {
        header('Location: admin.php');
        die();
    }

    die();
} else {
    $html = "";

    $html = printHeader($html);

    $html .= "\t\t\t<h1 id='loginHeader'>Login</h1>\n";
    $html .= "\t\t\t\t<p>Enter your login key to continue.</p>\n";
    $html .= "\t\t\t\t<form action=\"login.php\">\n";
    $html .= "\t\t\t\t\t<input type=\"password\" name=\"key\" placeholder=\"Login key\">\n";
    $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Login\">\n";
    $html .= "\t\t\t\t</form>\n";

    if (isset($_REQUEST['e']) && $_REQUEST['e'] == "true") {
        $html .= "\t\t\t\t<p class=\"error\">Invalid key.</p>\n";
    }

    $html = printFooter($html);

    print "$html";
}
?>
