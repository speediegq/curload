<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function printHeader($html) {
    include "config.php";

    $html .= "<!DOCTYPE html>\n";
    $html .= "<html>\n";
    $html .= "\t<head>\n";
    $html .= "\t\t<meta name=\"description\" content=\"$instanceDescription\">\n";
    $html .= "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";

    if (file_exists($Icon)) $html .= "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
    if (file_exists($Stylesheet)) $html .= "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
    if (file_exists($javaScript)) $html .= "\t\t<script src=\"$javaScript\"></script>\n";

    $html .= "\t\t<title>$instanceName</title>\n";
    $html .= "\t\t<div class=\"bar\">\n";
    $html .= "\t\t\t<span id='titleSpan' class='title'>\n";
    if (file_exists($Logo)) $html .= "\t\t\t\t<img src=\"$Logo\" id=\"titleLogo\" class=\"title\" width=\"$logoHeaderSize\" height=\"$logoHeaderSize\">\n";
    $html .= "\t\t\t\t<small id='title'><a id='title' href=\"/\">$instanceName</a></small>\n";

    if (!isset($_COOKIE[$cookieName])) {
        $html .= "\t\t\t\t<small id='login'><a href=\"login.php\">Log in</a></small>\n";
    } else {
        $html .= "\t\t\t\t<small id='logout'><a href=\"login.php?logout=true\">Log out</a></small>\n";
    }

    if (isset($_COOKIE[$cookieTypeName]) && $_COOKIE[$cookieTypeName] == 2) {
        $html .= "\t\t\t\t<small id='administration'><a id='administration' href=\"admin.php\">Administration</a></small>\n";
    }

    $html .= "\t\t\t</span>\n";
    $html .= "\t\t</div>\n";
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
