<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function main() {
    include "config.php";
    include "core.php";

    $html = "";
    $html = printHeader($html);

    $html .= "\t\t\t<h1>speedie's super awesome file uploader junk</h1>\n";
    $html .= "\t\t\t<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">Select file to upload<br><input type=\"file\" name=\"file\" id=\"file\"><br><input type=\"text\" name=\"key\" placeholder=\"Upload key here\"><br><input type=\"submit\" value=\"Upload selected file\" name=\"web\"></form>\n";
    $html .= "\t\t\t<p>Max file size: $maxFileSize MB</p>\n";
    $html .= "\t\t\t<a href=\"https://git.speedie.site/speedie/curload\">source code</a>\n";

    if (isset($_COOKIE[$cookieName])) {
        $html .= "\t\t\t<p>Cookie found, how awesome is that?</p>\n";
    }

    $html = printFooter($html);

    print "$html";
}

main();

?>
