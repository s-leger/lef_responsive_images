<?php

/***************
 * Temporary variables
 */
$extensionKey = 'lef_responsive_images';

/***************
 * Register PageTS
 */


// TCEFORM
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    $extensionKey,
    'Configuration/PageTS/TCEFORM.txt',
    'Responsive Images: TCEFORM'
);
