<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php"?;
include "create-table.php";

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
} else {
    $Authorized = 0;
}
?>
