<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "core.php";
include "config.php";
include "create-table.php";

if (!isset($_COOKIE[$cookieName]) || !isset($_COOKIE[$cookieTypeName])) {
    header('Location: login.php?redir=admin');
    die();
} else if ($_COOKIE[$cookieTypeName] != 2) { // not allowed
    header('Location: /');
    die();
}

$html = "";
$html = printHeader($html);

// in case admin keys are disabled
if (!$enableAdminKeys || $enableAdminKeys == "false") {
    header('Location: /');
    die();
}

$html = printFooter($html);

print "$html";

?>
