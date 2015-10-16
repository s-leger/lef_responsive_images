<?php
namespace LEF\LefResponsiveImages\UserFunc;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResponsiveImagesUserFunc {	
	
	public $cObj;
	
	function debug($content='',$conf){
	
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'])){
			$settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'];
		} else {
			return 1200;
		}
		$refWidth 	= (int)$GLOBALS['TSFE']->register[$conf['refWidth']];
		$container	= (int)$GLOBALS['TSFE']->register[$conf['container']];
		$gutter 	= (int)$settings['gutter'];
		$imagecols  = isset($this->cObj->data['imagecols']) ? (int)$this->cObj->data['imagecols'] : 1;
		
		if (isset($conf['numberOfColumns']) && isset($settings['imageColumnsAdaptive']) && $settings['imageColumnsAdaptive']){
			$tmp = explode(',', $conf['numberOfColumns']);
			$numberOfColumns = count($tmp) >= $imagecols ? (int)$tmp[$imagecols-1] : 1;
		} else {
			$numberOfColumns = ((int)$GLOBALS['TSFE']->register['imageCount'] > 1) ? $imagecols : 1;
		}
		$content .= '<div class="debug">refWidth:'.$refWidth.' imagecols:'.$imagecols.'</div>';
		$content .= '<div class="debug">numberOfColumns:'.$numberOfColumns.' container:'.$container.'</div>';
		
		return $content;
	}
	
	function mediaQuery($content='', $conf){
		
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'])){
			$settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'];
		} 
		$content .= isset($conf['min']) ? '(min-width:' . $conf['min'] .'px)' : ''; 
		$content .= isset($conf['min']) && isset($conf['max']) ? ' AND ' : '';
		$content .= isset($conf['max']) ? '(max-width:' . ((int)$conf['max']-1) .'px)' : '';
		$content .= (isset($conf['min']) || isset($conf['max'])) && isset($conf['pixelDensity']) ? ' AND ' : '';
		$content .= isset($conf['pixelDensity']) && isset($settings['mediaQuery']) ? '(' . $settings['mediaQuery'] . ':' . $conf['pixelDensity'] . ')' :'';
		return $content;
	
	}
	
	function srcsetSize($content='', $conf){
	
		if (isset($conf['if.']) && !$this->cObj->checkIf($conf['if.'])){
			return $content;
		}
		$width  = isset($conf['width']) ? (int)$GLOBALS['TSFE']->register[$conf['width']] : 1200;
		$screen = (int)$conf['screen'];
		$content .= $this->mediaQuery($content, $conf);
		$content .= ' '.(100*$width/$screen).'vw,' . LF;
		return $content;
	
	}
	
	function width($content='',$conf){	
		
		
		if (is_array($GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'])){
			$settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['lef_responsive_images.'];
		} else {
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
		$conf['container']
		$conf['screen']
		$conf['numberOfColumns']
		$conf['disableUserDefinedWidth']
		$conf['disableImageAboveText']
		*/
		
		$refWidth 	= (int)$GLOBALS['TSFE']->register[$conf['refWidth']];
		$container	= (int)$GLOBALS['TSFE']->register[$conf['container']];
		$gutter 	= (int)$settings['gutter'];
		$imagecols  = isset($this->cObj->data['imagecols']) ? (int)$this->cObj->data['imagecols'] : 1;
		$disableImageAboveText 	 = isset($conf['disableImageAboveText']) && $conf['disableImageAboveText'];
		$disableUserDefinedWidth = isset($conf['disableUserDefinedWidth']) && $conf['disableUserDefinedWidth'];
		$withoutGutter = isset($conf['withoutGutter']) ? $conf['withoutGutter'] : 1;
		
		if (isset($conf['numberOfColumns']) && isset($settings['imageColumnsAdaptive']) && $settings['imageColumnsAdaptive']){
			$tmp = explode(',', $conf['numberOfColumns']);
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
			} else	{
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

	
}