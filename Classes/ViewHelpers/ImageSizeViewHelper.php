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
 *  furnished to do so, subject to the following conditions:
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
 *
 *  Compute real image size according your layout
 *  Use this on content elements changing layout via bootstrap col-xx-yy classes
 *  or with user defined margins .. see arguments below.
 *
 *  NB: standard frames (well, jumbotron, indent) are allready taken into account
 *      in /Partials/Media/Gallery.html
 *
 *  eg:
 *  <div class="carousel-image col-xs-8" style="margin:40px;">
 *     <lef:imageSize xs="8" marginxs="40">
 *
 */

/**
 * @author Stephen Leger <stephen@3dservices.ch>
 */

use LEF\LefResponsiveImages\Utility\ResponsiveImagesUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImageSizeViewHelper extends AbstractViewHelper implements CompilableInterface
{

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('as', 'string', 'Variable name used to store image sizes', false, 'imagesize');

        $conf = ResponsiveImagesUtility::getSettings();
        $breakpoints = array_keys($conf['grid.']['container.']);
        foreach ($breakpoints as $k => $breakpoint) {
            $this->registerArgument($breakpoint, 'float', 'Number of columns for ' . $breakpoint . ' (may be a float for non standard columns number)', false, 0);
        }
        foreach ($breakpoints as $k => $breakpoint) {
            $this->registerArgument('margin' . $breakpoint, 'integer', 'Margin for ' . $breakpoint . ' in pixels', false, 0);
        }
        $this->registerArgument('fluid', 'float', 'Container fluid', false, -1);
        $this->registerArgument('border', 'integer', 'Border size arround each image', false, 0);

        // user defined fixed width, auto crop according
        $this->registerArgument('imagewidth', 'float', 'User defined width', false, 0);
        $this->registerArgument('imageheight', 'float', 'User defined height', false, 0);
        // user defined crop and image ratio
        $this->registerArgument('ratio', 'float', 'Ratio height/width in %, will crop height when > 0', false, 0);
        $this->registerArgument('crop', 'string', 'vertical crop position in % 0 = center -100 = top 100 = bottom', false, '');
        $this->registerArgument('file', 'object', 'file properties', false, null);
        $this->registerArgument('store', 'boolean', 'Compute and store number of images per row using number of columns', false, false);
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
        $settings = ResponsiveImagesUtility::getSettings();
        
        $as = $arguments['as'];

        // retrieve and store current size
        $size = ResponsiveImagesUtility::backupImageSize($renderingContext, $settings, $as);

        // layout change, we are now in a container
        if ($arguments['fluid'] == 0 and $size['fluid'] == 1) {
            $newSize = ResponsiveImagesUtility::getDefault($settings, false);
        } else {
            $newSize = $size;
        }

        // border width wrapping all images
        if ($arguments['border'] > 0) {
            $newSize['border'] = 2*$arguments['border'];
        }

        // user defined image ratio, will crop either width or height
        if ($arguments['ratio'] > 0) {
            $newSize['ratio'] = $arguments['ratio'] / 100;
        }

        // specify the crop in percent : -100 = left or top 100 = right or bottom
        // eg for download files thumbs
        if ($arguments['crop'] !== '') {
            $newSize['crop'] = $arguments['crop'];
        }

        // user defined image size, will override all other settings
        if ($arguments['imagewidth'] > 0) {
            $newSize['imagewidth'] = $arguments['imagewidth'];
        }

        if ($arguments['imageheight'] > 0) {
            $newSize['imageheight'] = $arguments['imageheight'];
        }

        // disabled when user defined size is set
        if ($arguments['imagewidth'] == 0 and $arguments['imageheight'] == 0) {

            $breakpoints = array_keys($settings['grid.']['container.']);
            //GeneralUtility::trimExplode(',', $settings['breakpoints'], true);
            $maxcols = intval($settings['grid.']['columns']);
            $gutter  = intval($settings['grid.']['gutter']);
            $margin = 0;
            $column = $maxcols;

            // breakpoints = xxs,xs,sm,md,lg

            // inherit sizes from smallest to bigger
            foreach ($breakpoints as $k => $breakpoint) {
                $argmargin = intval($arguments['margin' . $breakpoint]);
                // number of columns (may be a float for non standard columns number)
                $argcolumn = floatval($arguments[$breakpoint]);
                if ($argmargin > 0) {
                    $margin = 2 * $argmargin;
                }
                if ($argcolumn > 0) {
                    $column = $argcolumn;
                }
                $newSize[$breakpoint]['width'] -= $margin;
                if ($arguments['store']) {
                    // store images per row, allow to dynamically divide available space
                    // taking variable borderspace in account
                    $newSize[$breakpoint]['cols']  = round($maxcols/$column);
                } else {
                    // compute available space
                    $newSize[$breakpoint]['width'] = ($newSize[$breakpoint]['width'] + $gutter) / $maxcols * $column - $gutter;
                }
            }
        }

        $renderingContext->getTemplateVariableContainer()->add($as, $newSize);

        $content = $renderChildrenClosure();

        // restore only if the tag is closed and has childrens
        if ($content != '') {
            ResponsiveImagesUtility::restoreImageSize($renderingContext, $size, $as);
        }

        return $content;
    }
}
