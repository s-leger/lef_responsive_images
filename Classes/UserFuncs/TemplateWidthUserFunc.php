<?php
  namespace LEF\LefResponsiveImages\UserFuncs;

  /*
  *  The MIT License (MIT)
  *
  *  Copyright (c) 2014 Benjamin Kott, http://www.bk2k.info
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
  *  Store template sizes to register in order to generate images with the right size
  *
  *  Usage in fluid pages template : 
  *
  *  @param fluid : 0 (default) when wrapped into .container, 1 when outside of .container
  *  @param xs, sm, md, lg : default 12, columns count as in your bootstrap columns class, upper inherits from lower
  *  @param marginxs, marginsm, marginmd, marginlg : default 0, margins left and right in pixels, upper inherits from lower
  *
  *  <div class="col-xs-8 col-sm-6">
  *     <f:cObject typoscriptObjectPath="lib.dynamicContent" data="{pageUid: '{data.uid}', colPos: '0', fluid: '0', xs: '8', sm: '6', marginxs: '40'}"/>
  *  
  *
  *  Gridelements integration sample :
  *
  *  columns.0 {
  *     renderObj = COA
  *     renderObj {
  *         10 = LOAD_REGISTER
  *         20 = USER
  *         20 {
  *             userFunc = LEF\LefResponsiveImages\UserFuncs\TemplateWidthUserFunc->storeToRegister
  *             # number of columns
  *             xs = TEXT
  *             xs.field = parentgrid_flexform_width_column_xs_1
  *             sm < .xs
  *             sm.field = parentgrid_flexform_width_column_sm_1
  *             md < .xs
  *             md.field = parentgrid_flexform_width_column_md_1
  *             lg < .xs
  *             lg.field = parentgrid_flexform_width_column_lg_1
  *
  *             # margins left and right
  *             marginxs < .xs
  *             marginxs.value = 0
  *             marginsm < .xs
  *             marginsm.value = 0 
  *             marginmd < .xs
  *             marginmd.value = 0 
  *             marginlg < .xs
  *             marginlg.value = 0 
  *
  *             # 1 when content is outside .container  
  *             fluid = 0   
  *         }
  *         30 =< tt_content
  *         40 = RESTORE_REGISTER
  *     }
  *  }
  */

  /**
  * @author Stephen Leger
  */

use LEF\LefResponsiveImages\Utility\ResponsiveImagesUtility;

class TemplateWidthUserFunc
{
    /**
    * Reference to the parent (calling) cObject set from TypoScript
    */
    public $cObj;

    /**
     * @var array $settings
     */
    protected $settings;

    /**
     * @var bool $fluid
     */
    protected $fluid = 0;
  
    /**
     * @var array $imagesize
     */
    protected $imagesize = array();

    /**
     *
     * @param array $conf
     * @param string $key
     * @param float $default
     * @param float $min
     * @return float $val
     */
    private function getConf(&$conf, $key, $default, $min = 0)
    {
        if (isset($conf[$key]) || is_array($conf[$key . '.'])) {
            $val = floatval($this->cObj->stdWrap($conf[$key], $conf[$key . '.']));
        } else {
            $val = $default;
        }
        if ($val < $min) {
            return $default;
        }
        return $val;
    }

    /**
     * Compute margins
     * @param array $conf
     * @return void
     */
    private function initializeMargins(&$conf)
    {
        $current = 0;
        foreach ($this->imagesize as $key => $size) {
            if (is_array($size) and isset($size['width'])) {
                $current = $this->getConf($conf, "margin" . $key, $current, 0);
                $this->imagesize[$key]['width'] -= $current;
            }
        }
    }

    /**
     * Compute columns width
     * @param array $conf
     * @return void
     */
    private function initializeColumns(&$conf)
    {
        $current = $this->settings['grid.']['columns'];
        foreach ($this->imagesize as $key => $size) {
            if (is_array($size) and isset($size['width'])) {
                $current = $this->getConf($conf, $key, $current, 1);
                $this->imagesize[$key]['width'] = ($size['width'] + $this->settings['grid.']['gutter']) * ($current / $this->settings['grid.']['columns']) - $this->settings['grid.']['gutter'];
            }
        }
    }

    /**
     * Compute columns width
     * @param array $conf
     * @return void
     */
    private function initializeColumnsDivider(&$conf)
    {
        $current = 1;
        foreach ($this->imagesize as $key => $size) {
            if (is_array($size) and isset($size['width'])) {
                $current = $this->getConf($conf, $key, $current, $current);
                $this->imagesize[$key]['width'] = ($size['width'] + $this->settings['grid.']['gutter']) / $current  - $this->settings['grid.']['gutter'];
            }
        }
    }

    /**
     * Retrieve sizes from register
     * @return void
     */
    private function getTemplateSize()
    {
        $register = ResponsiveImagesUtility::getImageSizeFromRegister();
        // handle nested templates or content elements changing layout like gridelements
        if ($register) {
            $this->imagesize = $register;
            if ($register['fluid'] == 0) {
                $this->fluid = 0;
            }
        } else {
            $this->imagesize = ResponsiveImagesUtility::getDefault($this->settings, $this->fluid);
        }
    }

    /**
     * Store sizes to register
     * @return void
     */
    private function registerTemplateSize()
    {
        if ($this->fluid) {
            $this->imagesize['fluid'] = 1;
        }
        ResponsiveImagesUtility::setImageSizeToRegister($this->imagesize);
    }

    /**
    * Set number of items in row instead of columns
    * Allow different sizes than regular ones (like 5 items)
    * @param string $content
    * @param array $conf
    * @return string $content
    */
    public function divide($content = '', $conf = array())
    {
        $this->settings = ResponsiveImagesUtility::getSettings();

        // fluid set in template config
        $this->fluid = $this->getConf($conf, 'fluid', 0);
        $this->getTemplateSize();
        $this->initializeMargins($conf);
        $this->initializeColumnsDivider($conf);
        $this->registerTemplateSize();
        return $content;
    }

    /**
    * Set a number of columns (12)
    * @param string $content
    * @param array $conf
    * @return string $content
    */
    public function storeToRegister($content = '', $conf = array())
    {
        $this->settings = ResponsiveImagesUtility::getSettings();

        // fluid set in template config
        $this->fluid = $this->getConf($conf, 'fluid', 0);
        $this->getTemplateSize();
        $this->initializeMargins($conf);
        $this->initializeColumns($conf);
        $this->registerTemplateSize();
        return $content;
    }
}
