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
 */

/**
 * @author Stephen Leger <stephen@3dservices.ch>
 */
use LEF\LefResponsiveImages\Utility\ResponsiveImagesUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
* @author Stephen Leger
*/
class MediaQueryViewHelper extends AbstractViewHelper implements CompilableInterface
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('min', 'string', 'Breakpoint name min', false, '');
        $this->registerArgument('max', 'string', 'Breakpoint name max', false, '');
        $this->registerArgument('short', 'int', 'short tag without @media', false, 0);
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

        if (!$arguments['short'] and ($arguments['min'] !== '' or $arguments['max'] !== '')) {
            $content .= '@media ';
        }

        if ($arguments['min'] !== '') {
            $content .= '(min-width: ' . $settings['grid.']['screen.'][$arguments['min']] . 'px)';
            if ($arguments['max'] !== '') {
                $content .= ' and ';
            }
        }

        if ($arguments['max'] !== '') {
            $content .= '(max-width: ' . (intval($settings['grid.']['screen.'][$arguments['max']]) - 1) . 'px)';
        }

        if (!$arguments['short'] and ($arguments['min'] !== '' or $arguments['max'] !== '')) {
            $content .= '{';
        }

        $content .= $renderChildrenClosure();

        if (!$arguments['short'] and ($arguments['min'] !== '' or $arguments['max'] !== '')) {
            $content .= '}';
        }

        return $content;
    }
}
