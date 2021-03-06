<?php
namespace LEF\LefResponsiveImages\DataProcessing;

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
 * Load file metadata and ensure image width are there.
 *
 * Example TypoScript configuration:
 *
 * 10 = LEF\LefResponsiveImages\DataProcessing\ImageMetadataProcessor
 * 10 {
 *   as = files
 *  }
 * 
 *
 */
/**
 * @author Stephen Leger <stephen@3dservices.ch>
 */

use LEF\LefResponsiveImages\Utility\FileMetadataUtility;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
  
class ImageMetadataProcessor implements DataProcessorInterface
{
    /**
    * The content object renderer
    *
    * @var ContentObjectRenderer
    */
    public $cObj;

    /**
    * @var array
    */
    protected $defaults = [
        'as' => 'files'
    ];

    /**
    * MetaData Repository
    *
    * @var TYPO3\CMS\Core\Resource\Index\MetaDataRepository $MetaDataRepository
    */
    protected $MetaDataRepository;

    /**
    * The processor configuration
    *
    * @var array
    */
    protected $processorConfiguration;

    /**
    * @var ContentDataProcessor
    */
    protected $contentDataProcessor;

    /**
    * Constructor
    */
    public function __construct()
    {
        $this->contentDataProcessor = GeneralUtility::makeInstance(ContentDataProcessor::class);
        $this->MetaDataRepository = GeneralUtility::makeInstance(MetaDataRepository::class);
    }

    /**
    * Get configuration value from processorConfiguration
    *
    * @param string $key
    * @return string
    */
    protected function getConfigurationValue($key)
    {
        return $this->cObj->stdWrapValue($key, $this->processorConfiguration, $this->defaults[$key]);
    }
    /**
    * Update image metadata
    *
    * @param TYPO3\CMS\Core\Resource\File $originalFile
    * @return void
    */
    protected function updateMetadata(&$file)
    {
        $fileNameAndPath = $file->getForLocalProcessing(false);
        $imageInfo = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Type\\File\\ImageInfo', $fileNameAndPath);
        $additionalMetaInformation = array(
            'width' => $imageInfo->getWidth(),
            'height' => $imageInfo->getHeight()
        );
        $this->MetaDataRepository->update($file->getUid(), $additionalMetaInformation);
    }

    /**
    * @param ContentObjectRenderer $cObj The data of the content element or page
    * @param array $contentObjectConfiguration The configuration of Content Object
    * @param array $processorConfiguration The configuration of this processor
    * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
    * @return array the processed data as key/value store
    */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $this->processorConfiguration = $processorConfiguration;
        $this->cObj = $cObj;

        // Get Configuration
        $as = $this->getConfigurationValue('as');

        // load / Generate metadata
        foreach ($processedData[$as] as $k => $file) {
            if (!FileMetadataUtility::hasDimension($file)) {
                $this->updateMetadata($file);
            }
        }

        return $processedData;
    }
}
