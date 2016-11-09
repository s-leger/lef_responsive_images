<?php
if (!defined('TYPO3_MODE')) {
  die('Access denied.');
}
/***************
 * Make the extension configuration accessible
 */
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}


/***************
 * Default TypoScript
 */
 \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Bootstrap3 responsive images');


/***************
 * Register Icons
 */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'content-beside-text-img-intext-left',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-beside-text-img-intext-left.svg']
);
$iconRegistry->registerIcon(
    'content-beside-text-img-intext-right',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-beside-text-img-intext-right.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-grid',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-grid.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-row-right',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-row-right.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-row-justify',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-row-justify.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-row-left',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-row-left.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-col-top',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-col-top.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-col-justify',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-col-justify.svg']
);
$iconRegistry->registerIcon(
    'content-images-layout-col-bottom',
    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
    ['source' => 'EXT:lef_responsive_images/Resources/Public/Icons/ContentElements/content-images-layout-col-bottom.svg']
);
  
/***************
 * Reset extConf array to avoid errors
 */
if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY])) {
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY] = serialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
}
