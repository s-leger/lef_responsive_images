<?php
    namespace LEF\LefResponsiveImages\Controller;

    /**
        * This file is part of the TYPO3 CMS project.
        *
        * It is free software; you can redistribute it and/or modify it under
        * the terms of the GNU General Public License, either version 2
        * of the License, or any later version.
        *
        * For the full copyright and license information, please read the
        * LICENSE.txt file that was distributed with this source code.
        *
        * The TYPO3 project - inspiring people to share!
    */
    use \TYPO3\CMS\Core\Utility\GeneralUtility;
    use \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
    use \TYPO3\CMS\CssStyledContent\Controller\CssStyledContentController;
    use \TYPO3\CMS\Core\Utility\MathUtility;

    class ResponsiveImagesController  extends CssStyledContentController {

        function mediaQuery($conf, $settings){
            $mediaQuery = "";
            $mediaQuery .= isset($conf['min']) ? '(min-width:' . $conf['min'] .'px)' : '';
            $mediaQuery .= isset($conf['min']) && isset($conf['max']) ? ' AND ' : '';
            $mediaQuery .= isset($conf['max']) ? '(max-width:' . ((int)$conf['max']-1) .'px)' : '';
            $mediaQuery .= (isset($conf['min']) || isset($conf['max'])) && isset($conf['pixelDensity']) ? ' AND ' : '';
            $mediaQuery .= isset($conf['pixelDensity']) && isset($settings['mediaQuery']) ? '(' . $settings['mediaQuery'] . ':' . $conf['pixelDensity'] . ')' :'';
            return $mediaQuery;
        }

        function width($conf,$settings, $key){


            if (!is_array($settings)){
                return 1200;
            }
            /*
                $settings['borderThick']
                $settings['borderSpace']
                $settings['colSpace']
                $settings['gutter']
                $settings['userWidthProportional']
                $settings['imageColumnsAdaptive']
                $settings['imageAboveTextWidthRatio']
                $conf['screen']
                $conf['numberOfColumns']
                $conf['disableUserDefinedWidth']
                $conf['disableImageAboveText']
            */

            $refWidth   = (int)$GLOBALS['TSFE']->register[$conf['refWidth']];
            $container  = (int)$GLOBALS['TSFE']->register['width_' . substr($key, 0, -1) ];
            $gutter     = (int)$settings['gutter'];

            $imagecols  = isset($this->cObj->data['imagecols']) ? (int)$this->cObj->data['imagecols'] : 1;
            $disableImageAboveText   = isset($conf['disableImageAboveText']) && $conf['disableImageAboveText'];
            $disableUserDefinedWidth = isset($conf['disableUserDefinedWidth']) && $conf['disableUserDefinedWidth'];
            $withoutGutter = isset($conf['withoutGutter']) ? $conf['withoutGutter'] : 1;

            if (isset($conf['numberOfColumns']) && isset($settings['imageColumnsAdaptive']) && $settings['imageColumnsAdaptive']){
                $tmp =  GeneralUtility::trimExplode(',', $conf['numberOfColumns'], TRUE);
                $numberOfColumns = count($tmp) >= $imagecols ? (int)$tmp[$imagecols-1] : 1;
                } else {
                $numberOfColumns = ((int)$GLOBALS['TSFE']->register['imageCount'] > 1) ? $imagecols : 1;
            }

            if ((!$disableUserDefinedWidth) && isset($this->cObj->data['imagewidth']) && $this->cObj->data['imagewidth']){
                // User defined width
                // assume the width is given for largest screen
                $width = (int)$this->cObj->data['imagewidth'];
                if ($settings['userWidthProportional']){
                    $width /= $refWidth*$container;
                }
                $width  = (!$GLOBALS['TSFE']->register['fluid'] && $container < $width) ? $container : $width;
                } else {
                // Automatic width
                if ($GLOBALS['TSFE']->register['fluid']){
                    $width = (int)$conf['screen'] + ($withoutGutter ? $gutter : 0);
                    } else  {
                    $width = $container;
                }
                if ((!$disableImageAboveText) && GeneralUtility::inList("17,18,25,26",$this->cObj->data['imageorient'])){
                    $width *= (int)$settings['imageAboveTextWidthRatio']/100;
                }

                $width -= $gutter;
            }


            if ($numberOfColumns > 1){
                $width -= ($settings['colSpace'] * ($numberOfColumns - 1));
                $width /= $numberOfColumns;
            }

            if ($this->cObj->data['imageborder']){
                $width -= 2*((int)$settings['borderThick']+(int)$settings['borderSpace']);
            }
            return $width;
        }

        /**
            * Preprocess configuration of image
            *
            * @param string $content
            * @param array $conf
            * @return string HTML Content for textpic
        */
        public function render_textpic($content="", $conf){

            $settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'];
            //   $content .= '<div>test borderThick' . $settings['borderThick'] .'</div>';

            // flag cType image / textpic
            $isImage = isset ($this->cObj->data['CType']) && ($this->cObj->data['CType'] == "image" || $this->cObj->data['CType'] == "textpic");

            // keys of sourceCollection
            $sourceCollectionKeys = array_keys($conf['1.']['sourceCollection.']);

            // artDirection Images count
            $artDirectionImagesCount = count($sourceCollectionKeys);

            // imageMode set the rendering type css / artdirection / standard
            $imageMode = 0;
            if (isset($conf['imageMode'])){
                if(is_array($conf['imageMode.'])){
                    $imageMode = (int)$this->cObj->cObjGetSingle($conf['imageMode'],$conf['imageMode.']);
                    }   else {
                    $imageMode = (int)$conf['imageMode'];
                }
            }

            // flag to enable art direction mode
            $artDirection = $imageMode % 2 == 1;

            // image layout and wrapping
            switch ($imageMode){
                case 1:{ // artDirection
                    //  setup imageWrap
                    $conf['imageStdWrap.']['dataWrap'] = $settings['imageWrap'];
                    $conf['imageStdWrapNoWidth.']['wrap'] = $settings['imageWrap'];
                    $conf['imageColumnStdWrap.']['dataWrap'] =  $settings['columnWrap'];
                }
                break;
                case 2:{    // cssBackground
                    $conf['imgList.']['maxItems'] =  1;
                }
                case 3:{    // cssBackground with art direction
                    // clean up layout
                    unset ($conf['imageStdWrap.']);
                    unset ($conf['imageStdWrapNoWidth.']);
                    unset ($conf['imageColumnStdWrap.']);
                    $conf['layout.']['50'] = 'TEXT';
                    $conf['layout.']['50.']['value'] = '###IMAGES###';
                    $conf['layout.']['key.']['override'] = 50;

                    // remove border
                    $conf['image_frames'] = 0;
                    $conf['imageborder'] = 0;
                    // disable link
                    $conf['image_zoom'] = 0;
                    unset($conf['image_link']);
                    // remove caption
                    unset($conf['caption']);
                    unset($conf['caption.']);
                    // force below center
                    $conf['textpos'] = 26;

                }
                break;
            }

            // setup imgObjNum
            $imgObjNums = array();
            $imgObjNums[] = 1;

            if ($artDirection){
                // limit number of images for art direction
                $conf['imgList.']['begin'] = 0;
                $conf['imgList.']['maxItems'] =  $artDirectionImagesCount;
                for ($i = 1; $i < $artDirectionImagesCount; $i++){
                    $imgObjNums[] = ($i+1);
                }
            }

            $conf['imgObjNum'] = implode(' || ',$imgObjNums);

            // count the number of images
            $imgList = trim($this->cObj->stdWrap($conf['imgList'], $conf['imgList.']));
            $imgs = GeneralUtility::trimExplode(',', $imgList, TRUE);
            $imgCount = count($imgs);

            // Check for ArtDirection images count
            if ($artDirection){
                if( $imgCount < $artDirectionImagesCount){
                    $content .= '<p style="background-color: yellow;"><b>ERROR:</b>Art Direction incorrect images count : '.$imgCount.' (need '.$artDirectionImagesCount.') </p>';
                    // $content .= ' imageMode:' .$imageMode .' imgList:'. $imgList . ' $conf["imgObjNum"]' . $conf['imgObjNum'];
                    return $content;
                }
            }

            // fallback width when something goes wrong with width
            $widthFallback = 800;
            if (isset($conf['widthFallback']) && MathUtility::canBeInterpretedAsInteger($conf['widthFallback'])){
                $widthFallback = $conf['widthFallback'];
            }

            // register frames width only for CType images and textpic
            $restoreRegister = false;
            if ($isImage){
                if (isset($conf['frames']) && isset($conf['frames.'])){
                    $restoreRegister = true;
                    $this->cObj->LOAD_REGISTER($conf['frames.'], 'LOAD_REGISTER');
                }
            }

            // baseUrl needed for css background images to fix ie behiavour on ajax call with base href
            // also allowing to get image from another domain (cookie free one)
            $baseUrl = "";
            if (isset($settings['baseUrl']) && isset($settings['baseUrl.'])){
                $baseUrl = $this->cObj->cObjGetSingle($settings['baseUrl'],$settings['baseUrl.']);
            }

            // prefix all images sources with $baseUrl only when config.absRefPrefix is not set
            $absRefPrefix ="";
            if (isset($settings['absRefPrefix']) && $settings['absRefPrefix'] == "all"){
                $absRefPrefix = $baseUrl;
            }

            // html css Selector (id or class) of target elements for css backgrounds
            $cssSelector    = '#c'.$this->cObj->data['uid'];
            if (isset($conf['cssSelector']) && is_array($conf['cssSelector.'])){
                $cssSelector = $this->cObj->cObjGetSingle($conf['cssSelector'],$conf['cssSelector.']);
            }

            // Allow cssSelector override by register
            if (isset( $GLOBALS['TSFE']->register['cssSelector'])){
                $cssSelector = $GLOBALS['TSFE']->register['cssSelector'];
            }

            $debug = '';

            $srcsetSizes = array();
            $layoutKey  = '';

            // Store the imgObjNum 1 configuration
            $imgConfCopy = $conf['1.'];

            // iterate over active imgObjNum to setup width mediaQuery and pixelDensity
            foreach ($imgObjNums as $k=>$imgObjNum ) {

                // copy imgObjNum 1 configuration as base
                $conf[$imgObjNum . '.'] = $imgConfCopy;
                $imgConf = &$conf[$imgObjNum . '.'];

                // setup width and mediaQuery
                if (is_array($imgConf['sourceCollection.'])){
                    // Setup Retina params if enabled
                    if ($settings['retina.']['enable']){
                        $i = 0;
                        foreach($imgConf['sourceCollection.'] as $key=>$sourceCollection){
                             if ((isset($sourceCollection['if.']) && !$this->cObj->checkIf($sourceCollection['if.'])) || ($artDirection && $i != $k)){
								unset ($imgConf['sourceCollection.'][$key]);
                                $i++;
                                continue;
                            }
							$i++;
                            $srcsetSize = "";
                            // datakey and pixelDensity
                            $imgConf['sourceCollection.'][$key]['dataKey'] = 'retina-' . $sourceCollection['dataKey'];
                            $imgConf['sourceCollection.'][$key]['pixelDensity'] = $settings['retina.']['pixeldensity'];

                            if (isset($sourceCollection['mediaQuery.'])){
                                $sourceCollection['mediaQuery.']['pixelDensity'] = $settings['retina.']['pixeldensity'];
                                $mediaQuery = $this->mediaQuery($sourceCollection['mediaQuery.'], $settings);
                                $srcsetSize .= $mediaQuery;
                                $imgConf['sourceCollection.'][$key]['mediaQuery'] = $mediaQuery;
                                unset($imgConf['sourceCollection.'][$key]['mediaQuery.']);
                            }
                            if (isset($sourceCollection['width.'])){
                                $screen = (int)$imgConf['sourceCollection.'][$key]['width.']['screen'];
                                $width  = $this->width($sourceCollection['width.'], $settings, $key);
                                $srcsetSize .= ' ' . (100*$width/$screen);
                                $imgConf['sourceCollection.'][$key]['width'] = $width;
                                $imgConf['sourceCollection.'][$key]['maxW']  = $width;
                                unset($imgConf['sourceCollection.'][$key]['width.']);
                            }
                            $srcsetSizes[] = $srcsetSize;
                            $imgConf['sourceCollection.']['retina-' . $key] = $imgConf['sourceCollection.'][$key];
                            unset($imgConf['sourceCollection.'][$key]);
                        }
                    }

                    // Setup Standard params
                    $i = 0;
                    foreach($imgConf['sourceCollection.'] as $key=>$sourceCollection){
                        if ((isset($sourceCollection['if.']) && !$this->cObj->checkIf($sourceCollection['if.'])) || ($artDirection && $i != $k)){
                            unset ($imgConf['sourceCollection.'][$key]);
                            $i++;
                            continue;
                        }
						$i++;
                        $srcsetSize = "";
                        if (isset($sourceCollection['mediaQuery.'])){
                            $mediaQuery = $this->mediaQuery($sourceCollection['mediaQuery.'], $settings);
                            $srcsetSize .= $mediaQuery;
                            $imgConf['sourceCollection.'][$key]['mediaQuery'] = $mediaQuery;
                            unset($imgConf['sourceCollection.'][$key]['mediaQuery.']);
                        }
                        if (isset($sourceCollection['width.'])){
                            $screen =(int)$imgConf['sourceCollection.'][$key]['width.']['screen'];
                            $width  = $this->width($sourceCollection['width.'], $settings, $key);
                            $srcsetSize .= ' ' . (100*$width/$screen);
                            $imgConf['sourceCollection.'][$key]['width'] = $width;
                            $imgConf['sourceCollection.'][$key]['maxW']  = $width;
                            unset($imgConf['sourceCollection.'][$key]['width.']);
                        }
//$debug .= '<pre>$k='.$k.' '.htmlspecialchars($srcsetSize) . '</pre>';
                        $srcsetSizes[] = $srcsetSize;
                    }
                }
            }

            unset($imgConfCopy);

            // setup srcsetsizes params for srcset layout
            $srcsetSizes = implode("vw," . LF, $srcsetSizes) . "vw";

            // iterate over active imgObjNum to setup layout
            foreach ($imgObjNums as $k=>$imgObjNum ) {
                $imgConf = &$conf[$imgObjNum . '.'];
                $layoutKey  = $imgConf['layoutKey'];

                // override layoutkey and render method for css background and art direction
                switch ($imageMode){
                    case 1:
                    $conf['renderMethod'] ='artdirection';
                    break;
                    case 2:
                    case 3:
                    $layoutKey  = 'cssbackground';
                    $conf['renderMethod'] = $layoutKey;
                    $imgConf['layoutKey'] = $layoutKey;
                    // disable link
                    unset ($imgConf['imageLinkWrap']);
                    unset ($imgConf['imageLinkWrap.']);

                    break;
                }

                // Dynamically build element layout for Artdirection image / cssBackground
                // replace absUrl and uid in layouts
                if (is_array($imgConf['layout.']) && is_array($imgConf['layout.'][$layoutKey.'.'])){
					$layout = &$imgConf['layout.'][$layoutKey.'.'];
					if (isset($layout['element'])){
						$element = $layout['element'];

						// Dynamically build element layout for Artdirection image / cssBackground
						if ($artDirection){
							if ($k == 0){
								$pos = strpos($element, "###SOURCECOLLECTION###");
								if (!is_bool($pos)){
									$element = substr($element, 0, $pos + strlen("###SOURCECOLLECTION###"));
								}
							} elseif ($k == $artDirectionImagesCount-1){
								$pos = strpos($element, "###SOURCECOLLECTION###");
								if (!is_bool($pos)){
									$element = substr($element, $pos);
								}
							} else {
								$element = '###SOURCECOLLECTION###';
							}
						}
// $debug .= '<pre>$k='.$k.' '.htmlspecialchars($element) . '</pre>';
						$element = str_replace('###BASE_URL###', 	$baseUrl, $element);
						$element = str_replace("###SRCSETSIZE###", 	$srcsetSizes, $element);
						$element = str_replace('###CSS_SELECTOR###',   $cssSelector,   $element);
						$element = str_replace('###ABSREFPREFIX###',   $absRefPrefix ,  $element);
						//      $element = str_replace('###DEBUG###',   $debug,  $element);
						$imgConf['layout.'][$layoutKey.'.']['element'] = $element;
					}
					if (isset($layout['source'])){
						$source = $layout['source'];
					//  $source = str_replace("###SRCSETSIZE###",$srcsetSizes,$source);
						$source = str_replace("###BASE_URL###", $baseUrl, $source);
						$source = str_replace('###CSS_SELECTOR###', $cssSelector, $source);
						$source = str_replace('###ABSREFPREFIX###',   $absRefPrefix ,   $source);
						//   $source = str_replace('###DEBUG###',   $debug,  $source);
						$imgConf['layout.'][$layoutKey.'.']['source'] = $source;
					}
				}

            }

          //  $debug = '<pre>' . print_r($conf, true) . '</pre>';
            // use regular process to keep standard behiavour
            $content .=  parent::render_textpic($content, $conf);

		//	$content .= $debug;

            // restore registers
            if ($restoreRegister){
                $this->cObj->LOAD_REGISTER(array(), 'RESTORE_REGISTER');
            }

            // put css background to header or leave inline
            if ($imageMode > 1 && !$settings['movecsstobody']){
                $GLOBALS['TSFE']->getPageRenderer()->addHeaderData( $content);
                return '';
            }
            //      $content .= $debug_layout;
            return $content;
        }
    }
?>