<?php
if (!defined('TYPO3_MODE')) {
        die ('Access denied.');
}

$GLOBALS['TCA']['tt_content']['columns']['imagecols'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imagecols',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            [
                '1',
                1
            ],
            [
                '2',
                2
            ],
            [
                '3',
                3
            ],
            [
                '4',
                4
            ],
            [
                '5',
                5
            ],
            [
                '6',
                6
            ],
            [
                '7',
                7
            ],
            [
                '8',
                8
            ],
            [
                '9',
                9
            ],
            [
                '10',
                10
            ]
        ],
        'default' => 2
    ],
    'displayCond' => 'FIELD:image_rendering:!=:4'
];
  
$GLOBALS['TCA']['tt_content']['columns']['imagewidth'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imagewidth',
    'config' => [
        'type' => 'input',
        'size' => '4',
        'max' => '4',
        'eval' => 'int',
        'range' => [
            'upper' => 1999,
            'lower' => 0,
        ],
        'default' => 0
    ],
    'displayCond' => 'FIELD:image_rendering:!=:4'
];

$GLOBALS['TCA']['tt_content']['columns']['imageheight'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageheight',
    'config' => [
        'type' => 'input',
        'size' => '4',
        'max' => '4',
        'eval' => 'int',
        'range' => [
            'upper' => 1999,
            'lower' => 0,
        ],
        'default' => 0
    ],
    'displayCond' => 'FIELD:image_rendering:!=:4'
];

$GLOBALS['TCA']['tt_content']['columns']['imageorient'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.0', 0, 'content-beside-text-img-above-center'],
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.3', 8, 'content-beside-text-img-below-center'],
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.6', 17, 'content-beside-text-img-intext-right'],
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.7', 18, 'content-beside-text-img-intext-left'],
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.9', 25, 'content-beside-text-img-right'],
            ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:imageorient.I.10', 26, 'content-beside-text-img-left']
        ],
        'selicon_cols' => 6,
        'default' => 0,
        'showIconTable' => true,
    ],
    'displayCond' => 'FIELD:image_rendering:!=:4'
 ];
$GLOBALS['TCA']['tt_content']['columns']['images_layout'] = [
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout',
    'exclude' => 1,
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
    'showIconTable' => 1,
    'selicon_cols' => 7,
        'items' => [
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.0', 0, 'content-images-layout-grid'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.1', 18, 'content-images-layout-row-left'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.2', 10, 'content-images-layout-row-justify'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.3', 34, 'content-images-layout-row-right'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.4', 17, 'content-images-layout-col-top'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.5', 9, 'content-images-layout-col-justify'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout_options.I.6', 33, 'content-images-layout-col-bottom']
        ],
        'default' => 0
    ],
    'displayCond' => 'FIELD:image_rendering:!=:4'
];
$GLOBALS['TCA']['tt_content']['columns']['images_quality'] = [
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality',
    'exclude' => 1,
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.0', 0],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.1', 60],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.2', 70],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.3', 75],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.4', 80],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.5', 85],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.6', 90],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.7', 95],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_quality_options.I.8', 100],
       ],
        'default' => 0
    ]
];
$GLOBALS['TCA']['tt_content']['columns']['image_rendering'] = [
    'exclude' => 1,
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_rendering',
    'config' => [
        'type' => 'select',
        'items' => [
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_rendering_options.I.0', '0'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_rendering_options.I.4', '4'],
            ['LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_rendering_options.I.9', '47'],
        ],
        'size' => 1,
        'maxitems' => 1,
    ]
];
$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= ',image_rendering';

$GLOBALS['TCA']['tt_content']['columns']['image_cssselector'] = [
    'exclude' => 1,
    'config' => [
        'type' => 'input',
        'max' => 256,
        'size' => 10,
        ],
    'label' => 'LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_cssselector',
    'displayCond' => 'FIELD:image_rendering:=:4'
];
  
$GLOBALS['TCA']['tt_content']['palettes']['imageblock'] = [
    'showitem' => '
        imageorient,
        imagecols,
        images_layout;LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.images_layout,
        --linebreak--,
        imagewidth,
        imageheight,
        images_quality, 
        --linebreak--,
        image_rendering;LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_rendering,
        image_cssselector;LLL:EXT:lef_responsive_images/Resources/Private/Language/Backend.xlf:field.image_cssselector
    '
]; 
  
/***************
 * Add FlexForms for content element configuration
 */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:lef_responsive_images/Configuration/FlexForms/Carousel.xml',
    'bootstrap_package_carousel'
);  