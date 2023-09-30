<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function createTables($sqlDB) {
    $Database = new SQLite3($sqlDB);

    /* administrator table
     * id (INTEGER PRIMARY KEY)
     * key (TEXT)
     * primaryadmin (INT)
     * numberofuploads (INT)
     * lastused (TEXT)
     * issued (TEXT)
     * ip (TEXT)
     * useragent (TEXT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS admins(id INTEGER PRIMARY KEY, key TEXT, primaryadmin INT, numberofuploads INT, lastused TEXT, issued TEXT, ip TEXT, useragent TEXT)");

    /* keys table
     * id (INTEGER PRIMARY KEY)
     * key (TEXT)
     * numberofuploads (INT)
     * lastused (INT)
     * issued (TEXT)
     * ip (TEXT)
     * useragent (TEXT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS keys(id INTEGER PRIMARY KEY, key TEXT, numberofuploads INT, lastused TEXT, issued TEXT, ip TEXT, useragent TEXT)");

    /* temporary keys table
     * id (INTEGER PRIMARY KEY)
     * key (TEXT)
     * numberofuploads (INT)
     * uploadsleft (INT)
     * lastused (TEXT)
     * issued (TEXT)
     * ip (TEXT)
     * useragent (TEXT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS tkeys(id INTEGER PRIMARY KEY, key TEXT, numberofuploads INT, uploadsleft INT, lastused TEXT, issued TEXT, ip TEXT, useragent TEXT)");

    /* uploads table
     * id (INTEGER PRIMARY KEY)
     * file (TEXT)
     * uploaddate (TEXT)
     * keyid (INT) (THIS IS THE ID OF THE KEY USED TO UPLOAD THE FILE)
     * keytype (INT)
     */
    $Database->exec("CREATE TABLE IF NOT EXISTS uploads(id INTEGER PRIMARY KEY, file TEXT, uploaddate TEXT, keyid INT, keytype INT)");

    return $Database;
}
?>
