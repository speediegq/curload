<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "core.php";
include "config.php";
include "create-table.php";

$Action = "";
$Authorized = 0;
$Primary = 0;

if (!isset($_COOKIE[$cookieName]) || !isset($_COOKIE[$cookieTypeName])) {
    header('Location: login.php?redir=admin');
    die();
} else if ($_COOKIE[$cookieTypeName] != 2) { // not allowed
    header('Location: /');
    die();
}

if (!isset($_REQUEST['action'])) {
    $Action = "files";
} else {
    $Action = $_REQUEST['action'];
}

// in case admin keys are disabled
if (!$enableAdminKeys || $enableAdminKeys == "false") {
    header('Location: /');
    die();
}

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM admins');

while ($line = $DatabaseQuery->fetchArray()) {
    if ($line['key'] == $_COOKIE[$cookieName] && $_COOKIE[$cookieName] != "" && $line['key'] != "" && ($enableKeys || $enableKeys == "true")) {
        $Authorized = 1;
        $Primary = $line['primaryadmin'];
        break;
    }
}

// not authorized
if ($Authorized != 1) {
    header('Location: /');
    die();
}

$html = "";
$html = printHeader($html);

$html .= "\t\t\t<h1>Administrator panel</h1>\n";
$html .= "\t\t\t\t<div class=\"adminLinks\">\n";
$html .= "\t\t\t\t\t<span id='adminSpan' class='title'>\n";

if ($Action == "files") {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=files\" id='sel'>Files</a>\n";
} else {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=files\">Files</a>\n";
}

if ($Action == "keys") {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=keys\" id='sel'>Keys</a>\n";
} else {
    $html .= "\t\t\t\t\t\t<a href=\"/admin.php?action=keys\">Keys</a>\n";
}

$html .= "\t\t\t\t\t</span>\n";
$html .= "\t\t\t\t</div>\n";

if ($Action == "files") {
    $DatabaseQuery = $Database->query('SELECT * FROM uploads');

    $html .= "\t\t\t\t<table class=\"adminFileView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"adminFileView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminFilename\">Filename</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUploadDate\">Upload date</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminKeyID\">Key ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminKeyType\">Key type</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Filename = $line['file'];
        $uploadDate = $line['uploaddate'];
        $keyID = $line['keyid'];

        if ($line['keytype'] == 0) {
            $keyType = "Key";
        } else if ($line['keytype'] == 1) {
            $keyType = "Temporary";
        } else if ($line['keytype'] == 2) {
            $keyType = "Administrator";
        } else {
            $keyType = "Unknown";
        }

        $html .= "\t\t\t\t\t<tr class=\"adminFileView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"adminID-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminFilename\">$Filename</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadDate\">$uploadDate</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKeyID\">$keyID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKeyType\">$keyType</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove.php?redir=admin&id=$ID\">Remove</a></td>\n";

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
} else if ($Action == "keys") {
    $html .= "\t\t\t\t<p class=\"adminWarning\">Administrator keys are not visible.</p>\n";
    $html .= "\t\t\t\t<table class=\"adminKeyView\">\n";
    $html .= "\t\t\t\t\t<tr class=\"adminKeyView\">\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminID\">ID</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminKey\">Key</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminNumberOfUploads\">Uploads</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUploadsLeft\">Uploads left</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminLastUsed\">Last used</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminIssued\">Issued</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminIP\">IP</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminUserAgent\">User agent</th>\n";
    $html .= "\t\t\t\t\t\t<th class=\"adminKeyType\">Key type</th>\n";
    $html .= "\t\t\t\t\t</tr>\n";

    $DatabaseQuery = $Database->query('SELECT * FROM admins');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($Primary != 1) {
            break;
        }

        $ID = $line['id'];
        $Key = $line['key'];
        $NumberOfUploads = $line['numberofuploads'];
        $UploadsLeft = "";
        $LastUsed = $line['lastused'];
        $Issued = $line['issued'];
        $IP = $line['ip'];
        $UserAgent = $line['useragent'];

        $keyType = "Administrator";
        $UploadsLeft = "∞";

        if ($line['primaryadmin'] == 1) {
            $keyType = "Primary Administrator";
        }

        $html .= "\t\t\t\t\t<tr class=\"adminKeyView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"id-2-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKey\">$Key</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminNumberOfUploads\">$NumberOfUploads</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadsLeft\">$UploadsLeft</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminLastUsed\">$LastUsed</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIssued\">$Issued</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIP\">$IP</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUserAgent\">$UserAgent</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKeyType\">$keyType</td>\n";

        if ($Primary == 1 && $line['primaryadmin'] != 1) { // primary admins cannot be removed
            $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove-key.php?redir=admin&id=$ID\">Remove</a></td>\n";
        }

        $html .= "\t\t\t\t\t</tr>\n";
    }

    $DatabaseQuery = $Database->query('SELECT * FROM keys');
    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Key = $line['key'];
        $NumberOfUploads = $line['numberofuploads'];
        $UploadsLeft = "";
        $LastUsed = $line['lastused'];
        $Issued = $line['issued'];
        $IP = $line['ip'];
        $UserAgent = $line['useragent'];

        $keyType = "Key";
        $UploadsLeft = "∞";

        $html .= "\t\t\t\t\t<tr class=\"adminKeyView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"id-0-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKey\">$Key</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminNumberOfUploads\">$NumberOfUploads</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadsLeft\">$UploadsLeft</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminLastUsed\">$LastUsed</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIssued\">$Issued</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIP\">$IP</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUserAgent\">$UserAgent</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKeyType\">$keyType</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove-key.php?redir=admin&id=$ID\">Remove</a></td>\n";
        $html .= "\t\t\t\t\t</tr>\n";
    }

    $DatabaseQuery = $Database->query('SELECT * FROM tkeys');
    while ($line = $DatabaseQuery->fetchArray()) {
        $ID = $line['id'];
        $Key = $line['key'];
        $NumberOfUploads = $line['numberofuploads'];
        $UploadsLeft = "";
        $LastUsed = $line['lastused'];
        $Issued = $line['issued'];
        $IP = $line['ip'];
        $UserAgent = $line['useragent'];

        $keyType = "Temporary";
        $UploadsLeft = $line['uploadsleft'];

        $html .= "\t\t\t\t\t<tr class=\"adminKeyView\">\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminID\" id=\"id-1-$ID\">$ID</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKey\">$Key</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminNumberOfUploads\">$NumberOfUploads</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUploadsLeft\">$UploadsLeft</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminLastUsed\">$LastUsed</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIssued\">$Issued</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminIP\">$IP</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminUserAgent\">$UserAgent</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminKeyType\">$keyType</td>\n";
        $html .= "\t\t\t\t\t\t<td class=\"adminRemove\"><a href=\"/remove-key.php?redir=admin&id=$ID\">Remove</a></td>\n";
        $html .= "\t\t\t\t\t</tr>\n";
    }

    $html .= "\t\t\t\t</table>\n";
}

$html = printFooter($html);

print "$html";

?>
