<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function printHead($html) {
    include "config.php";

    $html .= "<!DOCTYPE html>\n";
    $html .= "<html>\n";
    $html .= "\t<head>\n";
    $html .= "\t\t<meta name=\"description\" content=\"$instanceName is a simple file uploading site allowing users to upload files by authenticating using a key.\">\n";
    $html .= "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";

    if (file_exists($Icon)) $html .= "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
    if (file_exists($Stylesheet)) $html .= "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
    if (file_exists($javaScript)) $html .= "\t\t<script src=\"$javaScript\"></script>\n";

    $html .= "\t\t<title>$instanceName</title>\n";
    $html .= "\t</head>\n";
    $html .= "\t<body>\n";
    $html .= "\t\t<div class=\"content\">\n";

    return "$html";
}

function printFooter($html) {
    include "config.php";

    $html .= "\t\t</div>\n";
    $html .= "\t</body>\n";
    $html .= "</html>\n";

    return "$html";
}

?>
