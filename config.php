<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

$configFile             = "config.ini";
$Stylesheet             = "index.css";
$javaScript             = "index.js";
$Icon                   = "favicon.svg";
$Logo                   = "logo.svg";
$uploadDir              = "uploads/";
$maxFileSize            = "100";
$sqlDB                  = "curload.db";
$storeIP                = true;
$storeAgent             = true;
$storeIssued            = true;
$storeLastUsage         = true;
$storeUploads           = true;
$publicUploading        = false;
$renameDuplicates       = true;
$replaceOriginal        = false;
$logoHeaderSize         = 16;
$dateFormat             = "Y/m/d";
$instanceName           = "curload";
$cookieName             = "speedierocks";
$enableKeys             = true;
$enableAdminKeys        = true;
$enableTemporaryKeys    = true;
$enableUploadRemoval    = true;
$enableKeyUploadRemoval = true;

if (!file_exists($configFile)) {
    return;
}

/* load config file */
$configEntries = parse_ini_file($configFile);
$Stylesheet = $configEntries['css'];
$Icon = $configEntries['favicon'];
$Logo = $configEntries['logo'];
$uploadDir = $configEntries['upload_dir'];
$maxFileSize = $configEntries['max_size'];
$sqlDB = $configEntries['sqldb'];
$storeIP = $configEntries['store_ip'];
$storeAgent = $configEntries['store_user_agent'];
$storeIssued = $configEntries['store_issued'];
$storeLastUsage = $configEntries['store_last_usage'];
$storeUploads = $configEntries['store_number_of_uploads'];
$logoHeaderSize = $configEntries['logo_header_size'];
$dateFormat = $configEntries['date_format'];
$instanceName = $configEntries['instance_name'];
$publicUploading = $configEntries['public_uploading'];
$renameDuplicates = $configEntries['rename_duplicates'];
$replaceOriginal = $configEntries['replace_original'];
$enableKeys = $configEntries['enable_keys'];
$enableAdminKeys = $configEntries['enable_admin_keys'];
$enableTemporaryKeys = $configEntries['enable_temporary_keys'];
$enableUploadRemoval = $configEntries['enable_upload_removal'];
$enableKeyUploadRemoval = $configEntries['enable_key_upload_removal'];
$cookieName = $configEntries['cookie_name'];
$javaScript = $configEntries['javascript'];
?>
