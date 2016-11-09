<?php
namespace LEF\LefResponsiveImages\ViewHelpers;

/*
 *  The MIT License (MIT)
 *
 *  Copyright (c) 2016 Stephen Leger, http://www.3dservices.ch
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subbreakpointect to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

/**
 * @author Stephen Leger <stephen@3dservices.ch>
 * parts highly inspired by CMSExperts\Responsiveimages
 */

use LEF\LefResponsiveImages\Utility\FileMetadataUtility;
use LEF\LefResponsiveImages\Utility\ResponsiveImagesUtility;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GalleryViewHelper extends AbstractViewHelper implements CompilableInterface
{
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('as', 'string', 'Variable name used to store gallery', false, 'gallery');
        $this->registerArgument('imagesize', 'string', 'Variable name used to store image sizes', false, 'imagesize');
        $this->registerArgument('data', 'array', 'cObject data', true);
        $this->registerArgument('files', 'array', 'file properties', true);
        $this->registerArgument('settings', 'array', 'fluidtemplate settings', true);
    }

    /**
    * @return string the rendered string
    * @api
    */
    public function render()
    {
        return self::renderStatic(
            $this->arguments,
            $this->buildRenderChildrenClosure(),
            $this->renderingContext
        );
    }

   /**
    * compute rows / columns relations with respect to crop conf
    * @param int $colCount
    * @param string $w
    * @param string $h
    * @param array $gallery
    * @param array $imgSizes
    * @param array $relations
    * @return void
    */
    protected static function columnRelations($colCount, $w, $h, array &$gallery, array &$imgSizes, array &$relations)
    {
        foreach ($gallery['files'] as $k => &$files) {
            $imgSizes[$k] = array();
            foreach ($files['srcset'] as $breakpoint => &$file) {
                $fsize = FileMetadataUtility::getDimension($file['src']);
                $imgSizes[$k][$breakpoint] = $fsize[$w] / $fsize[$h];
                $relations[$breakpoint][(int)floor($k / $colCount)] += $imgSizes[$k][$breakpoint];
            }
        }
    }

    /**
    * Layout in rows or columns justify lengths
    * @param int $colCount
    * @param array $breakpoints
    * @param array $data
    * @param array $conf
    * @param array $settings
    * @param array $size
    * @param array $gallery
    * @return void
    */
    protected static function layoutJustify($colCount, &$breakpoints, &$data, &$conf, &$settings, &$size, &$gallery)
    {
        $imgCount = count($gallery['files']);
        $equalSize = 1;

        switch ($data['images_layout']) {
            case 9:   // no rows equalwidth
                if ($data['imagewidth']) {
                    $equalSize = intval($data['imagewidth']);
                }
                if ($data['imageheight']) {
                    $userDefinedSize = intval($data['imageheight']);
                }
                $heightratio = floatval($conf['images.']['layout.']['columnsheightratio']) / 100;
                $widthratio = 1;
                // swwap cols/row count
                $w = 'height';
                $h = 'width';
                $rowCount = $colCount;
                $colCount = ceil($imgCount/$colCount);
                // percent of total width for columns style
                $gallery['colpercent'] = 100 / $rowCount;
                break;

            case 10:  // no cols equalheight
                $rowCount = ceil($imgCount/$colCount);
                if ($data['imageheight']) {
                    $equalSize = intval($data['imageheight']);
                }
                if ($data['imagewidth']) {
                    $userDefinedSize = intval($data['imagewidth']);
                }
                $widthratio  = 0;
                $heightratio = 1;
                // fixed height so total stay in ratio
                //$widthratio  = floatval($conf["images."]["layout."]["columnsheightratio"]) / 100;
                $w = 'width';
                $h = 'height';
        }

        $gallery['imagecols'] = $colCount;
        $gallery['lastrow'] = ($rowCount-1) * $colCount;
        $relations = array();
        $imgSizes  = array();
        self::columnRelations($colCount, $w, $h, $gallery, $imgSizes, $relations);
        
        $accumSize = array();
        $accumDesiredSize = array();
        $rowIdx = -1;
        $nbImgs = $imgCount + $colCount;

        foreach ($gallery['files'] as $k => &$files) {
            if (($k % $colCount) == 0) {
                // A new row starts
                // Reset accumulated net width
                foreach ($files['srcset'] as $breakpoint => &$file) {
                    $accumSize[$breakpoint] = 0;
                    // Reset accumulated desired width
                    $accumDesiredSize[$breakpoint] = 0;
                }
                $nbImgs -= $colCount;
                $rowIdx++;
            }
            $j = 0;
            foreach ($files['srcset'] as $breakpoint => &$file) {

                if ($nbImgs < $colCount) {
                    $gutters = ($nbImgs - 1);
                } else {
                    $gutters = ($colCount - 1);
                }
                $borderspace =  intval($conf['images.']['borderspace.'][$breakpoint]);
                
                // allow user defined global size : width for no cols / height for no rows
                 if ($userDefinedSize and $j < intval($conf['grid.']['breakpoint'])) {
                    $maxW =  ($conf['grid.']['container.'][$breakpoint] * $heightratio) - ($gutters * $borderspace);
                    $net = min($maxW, $userDefinedSize / $conf['grid.']['container.'][$breakpoints[0]] * $conf['grid.']['container.'][$breakpoint] - ($gutters * $borderspace));
                } else {
                    $net =  ($size[$breakpoint]['width'] * $heightratio) - ($gutters * $borderspace);;
                }
              
                $filesTotalMax = $relations[$breakpoint][$rowIdx];

                // scale factor for images
                $scale = $filesTotalMax / $net;

                // This much size is available for the remaining images in this row/col (int)
                $availableSpace = $net - $accumSize[$breakpoint];

                // Theoretical size of resized image. (float)
                $desiredSpace = $imgSizes[$k][$breakpoint] / $scale;

                // Add this size. $accumDesiredSize becomes the desired position
                $accumDesiredSize[$breakpoint] += $desiredSpace;
                // Calculate size by comparing actual and desired position.
                // this evenly distributes rounding errors across all images in this row/col.
                $suggestedSize = round($accumDesiredSize[$breakpoint] - $accumSize[$breakpoint], 0);

                // finalImgSize may not exceed $availableSpace
                $finalImgSize = (int)min($availableSpace, $suggestedSize);
                $accumSize[$breakpoint] += $finalImgSize;
                $file[$w] =  $finalImgSize;
              
                $borderspace = intval($conf['images.']['borderspace.'][$breakpoint]);
                if ($equalSize > 1 and ($data['images_layout'] == 10 or $j < intval($conf['grid.']['breakpoint']))) {
                    $refsec = $equalSize * $size[$breakpoint]['width'] / $size[$breakpoints[0]]['width'] - $size['border'];
                } else {
                    // No row at some point need to remove any user defined width
                    // in order to prevent layout breaking
                    $refsec = ($size[$breakpoint]['width'] + $borderspace) / $rowCount - $borderspace - $size['border'];
                    
                }
                $file[$h] =  $refsec;
              
                // when fluid, image sizes depends on layout
                // percent of width for each column
                $file['percent'] = 100 * ($file['width'] + $borderspace) / ($size[$breakpoint]['width'] + $borderspace);
                $file[$w] -= $size['border'];
              
                $j ++;
                
            }
        }
    }

   /**
    * Layout "no rows and no cols"
    * @param int $colCount
    * @param array $breakpoints
    * @param array $data
    * @param array $conf
    * @param array $settings
    * @param array $size
    * @param array $gallery
    * @return void
    */
    protected static function layoutRowCol($colCount, &$breakpoints, &$data, &$conf, &$settings, &$size, &$gallery)
    {
        $imgCount = count($gallery['files']);
        $equalSize = 1;

        switch ($data['images_layout']) {
            case 17:  // no rows top
            case 33:  // no rows bottom
                if ($data['imagewidth']) {
                    $equalSize = intval($data['imagewidth']);
                }
                if ($data['imageheight']) {
                    $userDefinedSize = intval($data['imageheight']);
                }
                $heightratio = floatval($conf['images.']['layout.']['columnsheightratio']) / 100;
                $widthratio = 1;
                // swwap cols/row count
                $w = 'height';
                $h = 'width';
                $rowCount = $colCount;
                $colCount = ceil($imgCount/$colCount);
                // percent of total width for columns style
                $gallery['colpercent'] = 100 / $rowCount;
                break;

            case 34:  // no cols right
                // reverse array to reorder images while floating right
                $gallery['files'] = array_reverse($gallery['files']);
                // no break
            case 18:  // no cols left
                // no cols with equalheight
                $rowCount = ceil($imgCount/$colCount);
                if ($data['imageheight']) {
                    $equalSize = intval($data['imageheight']);
                }
                if ($data['imagewidth']) {
                    $userDefinedSize = intval($data['imagewidth']);
                }
                $heightratio = 1;
                // 0 let height depends on number of images in row
                $widthratio  = 0;
                // fixed height so total stay in ratio
                //$widthratio  = floatval($conf["images."]["layout."]["columnsheightratio"]) / 100;
                $w = 'width';
                $h = 'height';
        }

        $gallery['imagecols'] = $colCount;
        $gallery['lastrow'] = ($rowCount-1) * $colCount;
        
        $relations = array();
        $imgSizes  = array();
        self::columnRelations($colCount, $w, $h, $gallery, $imgSizes, $relations);

        foreach ($gallery['files'] as $k => &$files) {
            $j = 0;
            foreach ($files['srcset'] as $breakpoint => &$file) {
                // scale main size to available space, may be disabled to allow more freedom
                $borderspace =  intval($conf['images.']['borderspace.'][$breakpoint]);
                $colMax = max($relations[$breakpoint]);
                // allow user defined global size : width for no cols / height for no rows
                if ($userDefinedSize and $j < intval($conf['grid.']['breakpoint'])) {
                    $maxW =  ($conf['grid.']['container.'][$breakpoint] + $borderspace - ($colCount * $borderspace)) * $heightratio;
                    $net = min($maxW, $userDefinedSize / $conf['grid.']['container.'][$breakpoints[0]] * $conf['grid.']['container.'][$breakpoint] + $borderspace - ($colCount * $borderspace));
                } else {
                    $net = ($size[$breakpoint]['width'] + $borderspace - ($colCount * $borderspace)) * $heightratio;
                }
                $scale  = $colMax / $net;
                $main = $imgSizes[$k][$breakpoint] / $scale;
                
                $file[$w] = $main - $size['border'];
 
                if ($equalSize > 1 and (($data['images_layout'] & 2) == 2 or $j < intval($conf['grid.']['breakpoint']))) {
                    $sec = $equalSize * $size[$breakpoint]['width'] / $size[$breakpoints[0]]['width'];
                } else {
                    // only apply for no rows
                    $sec = ($size[$breakpoint]['width'] + $borderspace) / $rowCount - $borderspace;
                }
                $j ++;
                $file[$h] = $sec - $size['border'];

                // when fluid, image sizes depends on layout
                // percent of width for each images
                $file['percent'] = 100 * ($file['width'] + $borderspace) / ($size[$breakpoint]['width'] + $borderspace);
            }
        }
    }

   /**
    * Layout for css and items without layout
    * @param int $colCount
    * @param array $breakpoints
    * @param array $data
    * @param array $conf
    * @param array $settings
    * @param array $size
    * @param array $gallery
    * @return void
    */
    protected static function layoutNoLayout($colCount, &$breakpoints, &$data, &$conf, &$settings, &$size, &$gallery)
    {
        foreach ($gallery['files'] as $k => &$files) {
            foreach ($files['srcset'] as $breakpoint => &$file) {
                $file['height'] = $size[$breakpoint]['height'];
                $file['width']  = $size[$breakpoint]['width'];
            } 
            if (($data['image_rendering'] & 4) == 4) {
                $files['data']['selector'] = $data['image_cssselector'] ?  $data['image_cssselector'] : '#c' . $data['uid'];
            } else {
                $files['data']['selector'] = '#c' . $data['uid'];
            }  
        }
    }

   /**
    * Default layout in grid
    * @param int $colCount
    * @param array $breakpoints
    * @param array $data
    * @param array $conf
    * @param array $settings
    * @param array $size
    * @param array $gallery
    * @return void
    */
    protected static function layoutGrid($colCount, &$breakpoints, &$data, &$conf, &$settings, &$size, &$gallery)
    {
        // number of items by row
        foreach ($breakpoints as $key => $breakpoint) {
            $gallery['cols'][$breakpoint] = $size[$breakpoint]['cols'];
        }

        foreach ($gallery['files'] as $k => &$files) {
            $j = 0;
            foreach ($files['srcset'] as $breakpoint => &$file) {
                $borderspace =  intval($conf['images.']['borderspace.'][$breakpoint]);
                $width = 0;
                $height = 0;

                if ($data['imageheight'] > 0) {
                    $height = $data['imageheight'] * $size[$breakpoint]['width'] / $size[$breakpoints[0]]['width'] - $size['border'];
                }

                if (intval($data['imagewidth']) > 0 and $j < intval($conf['grid.']['breakpoint'])) {
                    $maxW  = ($conf['grid.']['container.'][$breakpoint] + $borderspace) / $size[$breakpoint]['cols'] - $borderspace - $size['border'];
                    $width = min($maxW, $data['imagewidth'] * $size[$breakpoint]['width'] / $size[$breakpoints[0]]['width'] - $size['border']);
                } else {
                    $width = ($size[$breakpoint]['width'] + $borderspace) / $size[$breakpoint]['cols'] - $borderspace - $size['border'];
                }
                $j ++;
                // left as float as with grid the sizes are always driven by layout
                // and this prevent pixel rounding errors to show
                $file['height'] = $height;
                $file['width']  = $width;
            }
        }
    }
    /**
    * @param array $arguments
    * @param \Closure $renderChildrenClosure
    * @param RenderingContextInterface $renderingContext
    * @return string $content
    */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $data = $arguments['data'];
        $as = $arguments['as'];
        $files = $arguments['files'];
        $settings = $arguments['settings'];

        $conf = ResponsiveImagesUtility::getSettings();
        $size = ResponsiveImagesUtility::getImageSize($renderingContext, $conf, $arguments['imagesize']);

        $breakpoints = array_keys($conf['grid.']['container.']);
        $breakpoints = array_reverse($breakpoints);
        
        $gallery = array(
            'error' => '',
            'imagecols' => 0,
            'cols' => array(),
            'files' => array()
        );


        // responsiveimage integration
        foreach ($files as $k => $file) {
            $originalRecord = [
                'uid' => $file->getUid()
            ];

            // check if it is a translation
            if ($file->getProperty('sys_language_uid')) {
                $originalRecord['_LOCALIZED_UID'] = $file->getProperty('sys_language_uid');
            }

            // fetch the files
            $alternativeFiles = $GLOBALS['TSFE']->sys_page->getFileReferences('sys_file_reference', 'alternativefile', $originalRecord);
            $sortedAlternativeFiles = array();
            foreach ($alternativeFiles as $alternativeFile) {
            // aternativetags must match settings.breakpoints
            $label = $alternativeFile->getProperty('alternativetag');
                $sortedAlternativeFiles[$label] = $alternativeFile;
            }

            $gallery['files'][$k] = array(
                'data' => array(
                    'placeholder' => array(
                        'width' => 0,
                        'height'=> 0
                    ),
                    'globalcaption' => 0,
                    'description' => $file->getProperty('description'),
                    'alternative' => $file->getProperty('alternative'),
                    'title' => $file->getProperty('title'),
                    'link' => $file->getProperty('link'),
                    'selector' => null,
                    'quality' => $data['images_quality'] ? $data['images_quality'] : $conf['images.']['quality'],
                ),
                'type' => $file->getType(),
                'srcset' => array()
            );

            $lastFile = $file;
            foreach ($breakpoints as $j => $breakpoint) {
                if ($sortedAlternativeFiles[$breakpoint]) {
                    // store it in case the next item is empty, so this one is used as well.
                    $lastFile = $sortedAlternativeFiles[$breakpoint];
                }
              
                $gallery['files'][$k]['srcset'][$breakpoint] = array(
                    'src' => $lastFile,
                    'width' => 0,
                    'height' => 0,
                    'cropw' => '',
                    'croph' => '',
                    'padding' => 0,
                    'percent' => 100,
                // note: mimetype is the originalFile mime type and may not be the same as final   
                //  'mimetype' => $lastFile->getMimeType(),
                );
            }
        }
      
        // css disable layouts
        if (($data['image_rendering'] & 0x0F) == 0x04) {
            $data['imagecols'] = 1;
            $data['images_layout'] = 1;  
        }
      
        $imgCount = count($gallery['files']);

        // limit number of cols
        $cols = intval($data['imagecols']);
        $colCount = $cols > 1 ? $cols : 1;
        if ($colCount > $imgCount) {
            $colCount = $imgCount;
        }

        switch ($data['images_layout']) {
            case 9:     // no rows equalwidth
            case 10:    // no cols equalheight
                self::layoutJustify($colCount, $breakpoints, $data, $conf, $settings, $size, $gallery);
                // individual caption may break layout
                $globalcaption = 1;
                break;
            case 17:    // no rows top or bottom - note: bottom dosen't work
            case 33:
            case 18:    // no cols left or right
            case 34:
                self::layoutRowCol($colCount, $breakpoints, $data, $conf, $settings, $size, $gallery);
                // individual caption may break layout
                $globalcaption = 1;
                break;
            default:
                $globalcaption = 0;
                switch ($data['image_rendering'] & 0x0F) {
                    case 0x04: // css
                        self::layoutNoLayout($colCount, $breakpoints, $data, $conf, $settings, $size, $gallery);
                        break;
                    default:  // regular bootstrap grid
                        self::layoutGrid($colCount, $breakpoints, $data, $conf, $settings, $size, $gallery);
                        break;
                }
        }

        // compute width and height and adbreakpointust cropping when needed
        foreach ($gallery['files'] as $k => &$files) {
            $ratio = 1;
            foreach ($files['srcset'] as $breakpoint => &$file) {
                $fileratio = FileMetadataUtility::getRatio($file['src']);

                // here we always have width, but only sometimes the height
                // on some layouts the width may be <= 0 so make it positive > 1
                $file['width'] = min(intval($conf['images.']['maxwidth']), max(1, $file['width'])); 

                // user defined ratio h/w
                if ($size['ratio']) {
                    $file['height'] = round($size['ratio'] * $file['width'], 0);
                } elseif (!$file['height']) {
                    // if there is no height specified use the one
                    $file['height'] = round($file['width'] * $fileratio, 0);
                }
                $file['height'] = min(intval($conf['images.']['maxheight']), max(1, $file['height']));
              
                // now we have both height and width
                // check if we need to crop and on witch side
                $ratio = $file['height'] / $file['width'];
                if ($fileratio > $ratio) {
                    $file['croph'] = 'c' . $size['crop'];
                } else {
                    $file['cropw']  = 'c' . $size['crop'];
                }

                // set a height as percent of width for a css padding-bottom if needed
                // for eg: prevent relayout of pages while loading images
                if (!$file['padding']) {
                    $file['padding'] = 100 * $ratio . "%";
                }
            }
            
            
            // note : art direction may have as many source / ratio as breakpoints
            // so placeholder may be a wrong image with wrong ratio
            if ($conf['images.']['placeholder.']['mode'] == 2) {
                $files['data']['placeholder']['width']  = intval($conf['images.']['placeholder.']['size']);
                $files['data']['placeholder']['height'] = floor($conf['images.']['placeholder.']['size'] * $ratio);
            }

            // allow css styling for image - eg: set absolute size in order to prevent relayout
            if (!$files['data']['selector']) {
                $files['data']['selector'] = "image-" . $data['uid'] ."-" . $k;
            }
            // disable individual caption as they may break some layouts
            $files['data']['globalcaption'] = $globalcaption;
        }

        if ($renderingContext->getTemplateVariableContainer()->exists($as) == true) {
            $renderingContext->getTemplateVariableContainer()->remove($as);
        }
        // usefull to print debug right before content.
        // $gallery['error'] .= '<pre>' . print_r($gallery['files'], true) . '</pre>';
        $renderingContext->getTemplateVariableContainer()->add($as, $gallery);
        $content = $renderChildrenClosure();
        $renderingContext->getTemplateVariableContainer()->remove($as);
        return $content;
    }
}
