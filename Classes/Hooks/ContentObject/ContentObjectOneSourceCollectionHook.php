<?php
namespace LEF\LefResponsiveImages\Hooks\ContentObject;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * stdWrap support of mediaQueries
 * 
 * 
 */
class ContentObjectOneSourceCollectionHook  implements \TYPO3\CMS\Frontend\ContentObject\ContentObjectOneSourceCollectionHookInterface {

	/**
	 * Renders One Source Collection
	 *
	 * @param array $sourceRenderConfiguration Array with TypoScript Properties for the imgResource
	 * @param array $sourceConfiguration
	 * @param string $oneSourceCollection already prerendered SourceCollection
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject Parent content object
	 * @internal param array $configuration Array with the Source Configuration
	 * @return string HTML Content for oneSourceCollection
	 */
	public function getOneSourceCollection(array $sourceRenderConfiguration, array $sourceConfiguration, $oneSourceCollection, \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject){

	    	if (isset($sourceConfiguration['mediaQuery'])) {
			$mediaQuery = $parentObject->stdWrap($sourceConfiguration['mediaQuery'], $sourceConfiguration['mediaQuery.']);
			$oneSourceCollection = str_replace($sourceConfiguration['mediaQuery'], $mediaQuery, $oneSourceCollection);
		}
		
		if (isset($sourceConfiguration['backgroundElementID'])) {
			$backgroundElementID = $parentObject->stdWrap($sourceConfiguration['backgroundElementID'], $sourceConfiguration['backgroundElementID.']);
			$oneSourceCollection = str_replace($sourceConfiguration['backgroundElementID'], $backgroundElementID, $oneSourceCollection);
		}
		
	return $oneSourceCollection;
	}
}