<?php
/***************
 * Add Content Element: Image
 */  
if (!is_array($GLOBALS['TCA']['tt_content']['types']['image'])) {
    $GLOBALS['TCA']['tt_content']['types']['image'] = [];
}
$GLOBALS['TCA']['tt_content']['types']['image'] = array_replace_recursive(
    $GLOBALS['TCA']['tt_content']['types']['image'],
    [
        'showitem' => '
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.images,
            --palette--;LLL:EXT:bootstrap_package/Resources/Private/Language/Backend.xlf:palette.alignment;imageblock,
            image,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.imagelinks;imagelinks,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility;visibility,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.extended,
            rowDescription'
    ]
);
