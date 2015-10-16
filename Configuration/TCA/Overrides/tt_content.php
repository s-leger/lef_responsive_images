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
        'tx_images_mode' =>array (
                'exclude' => 0,
                'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.imagesmode_options',
                'config' => array (
                        'type' => 'select',
                        'items' => array (
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.imagesmode_options.I.0', '0'),
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.imagesmode_options.I.1', '1'),
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.imagesmode_options.I.2', '2'),
                                array('LLL:EXT:lef_responsive_images/Resources/Private/Language/locallang_db.xlf:tt_content.imagesmode_options.I.3', '3'),
                       ),
                        'size' => 1,
                        'maxitems' => 1,
                )
        ),
        'tx_images_selector' =>array(
                'exclude' => 0,
        	'config' => array (
                        'type' => 'input',
                        'max' => 256,
                        'size' => 10,
                  ),
		 'label' => 'CSS selector', 
        ),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tt_content',
        $tca,
        1
);
$GLOBALS['TCA']['tt_content']['palettes']['image_settings']['showitem'] .= ',--linebreak--,tx_images_loading,tx_images_mode,tx_images_selector';