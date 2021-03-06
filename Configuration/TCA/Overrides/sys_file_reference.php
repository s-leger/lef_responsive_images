<?php
  
$GLOBALS['TCA']['sys_file_reference']['columns']['alternativefile'] = [
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.alternativefile',
    // hide when referenced from another sys_file_reference
    'displayCond' => 'FIELD:tablenames:!=:sys_file_reference',
    'l10n_mode' => 'exclude',
    'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
        'alternativefile',
        [
            'appearance' => array(
                'createNewRelationLinkTitle' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:button.addartdirection',
                'collapseAll' => 1,
                'expandSingle' => 1,
            ),
            'foreign_record_defaults' => array(
                'tablenames' => 'sys_file_reference'
            ),
            'foreign_types' => array(
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
                            'showitem' => '
                                alternativetag,--palette--;;filePalette
                            '
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
                            'showitem' => '
                                crop,alternativetag,--palette--;;filePalette
                            '
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
                            'showitem' => '
                                alternativetag,--palette--;;filePalette
                            '
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
                            'showitem' => '
                                alternativetag,--palette--;;filePalette
                            '
                        ),
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
                            'showitem' => '
                                crop,alternativetag,--palette--;;filePalette
                            '
                        ),
                    ),
        ],
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext']
    )
];
  

$GLOBALS['TCA']['sys_file_reference']['columns']['alternativetag'] = [
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints',
    // only show when referenced from another sys_file_reference
    'displayCond' => 'FIELD:tablenames:=:sys_file_reference',
    'config' => [
        'type' => 'select',
        'items' => [
        //  ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints.lg', 'lg'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints.md', 'md'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints.sm', 'sm'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints.xs', 'xs'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.breakpoints.xxs', 'xxs']
        ],
        'size' => 5
    ]
];
  
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'basicoverlayPalette', '--linebreak--,alternativetag');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'imageoverlayPalette', '--linebreak--,alternativefile');
// change label in the IRRE title bar
$GLOBALS['TCA']['sys_file_reference']['ctrl']['formattedLabel_userFunc'] = LEF\LefResponsiveImages\Service\UserFileInlineLabelService::class . '->getInlineLabel';
