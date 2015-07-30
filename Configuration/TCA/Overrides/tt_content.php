<?php
if (!defined('TYPO3_MODE')) {
        die ('Access denied.');
}

$tca = array (
        'tx_images_loading' => array (
                'exclude' => 0,
                'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.loadingmethod_options',
                'config' => array (
                        'type' => 'select',
                        'items' => array (
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.loadingmethod_options.I.0', '0'),
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.loadingmethod_options.I.1', '1'),
                        ),
                        'size' => 1,
                        'maxitems' => 1,
                )
        ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tt_content',
        $tca,
        1
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
        'tt_content',
        'image_settings',
        'tx_images_loading',
        'after:image_effects'
);
