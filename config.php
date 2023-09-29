<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

$Stylesheet          = "index.css";
$Icon                = "favicon.svg";
$uploadDir           = "uploads/";
$maxFileSize         = "100";
$sqlDB               = "curload.db";
$storeIP             = true;
$storeAgent          = true;
$storeIssued         = true;
$storeLastUsage      = true;
$storeUploads        = true;
$publicUploading     = false;
$renameDuplicates    = true;
$replaceOriginal     = false;
$dateFormat          = "Y/m/d";
$instanceName        = "curload";
$enableKeys          = true;
$enableAdminKeys     = true;
$enableTemporaryKeys = true;

define('CONFIG_FILE', 'config.ini');

if (!file_exists(CONFIG_FILE)) {
    return;
}

/* load config file */
$configEntries = parse_ini_file(CONFIG_FILE);
$Stylesheet = $configEntries['css'];
$Icon = $configEntries['favicon'];
$uploadDir = $configEntries['upload_dir'];
$maxFileSize = $configEntries['max_size'];
$sqlDB = $configEntries['sqldb'];
$storeIP = $configEntries['store_ip'];
$storeAgent = $configEntries['store_user_agent'];
$storeIssued = $configEntries['store_issued'];
$storeLastUsage = $configEntries['store_last_usage'];
$storeUploads = $configEntries['store_number_of_uploads'];
$dateFormat = $configEntries['date_format'];
$instanceName = $configEntries['instance_name'];
$publicUploading = $configEntries['public_uploading'];
$renameDuplicates = $configEntries['rename_duplicates'];
$replaceOriginal = $configEntries['replace_original'];
$enableKeys = $configEntries['enable_keys'];
$enableAdminKeys = $configEntries['enable_admin_keys'];
$enableTemporaryKeys = $configEntries['enable_temporary_keys'];
?>
