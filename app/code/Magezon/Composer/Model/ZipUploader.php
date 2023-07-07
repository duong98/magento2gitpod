<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductAttachments
 * @copyright Copyright (C) 2020 Magezon (https://www.magezon.com)
 */

namespace Magezon\Composer\Model;

class ZipUploader extends AbstractUploader
{
    const BASE_TMP_PATH = 'packages/tmp/files/';
    const BASE_PATH = 'packages/files/';

    /**
     * @return string
     */
    public function getBaseTmpPath()
    {
        return self::BASE_TMP_PATH;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return self::BASE_PATH;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->dataHelper->getFileExtension() ? explode(',', $this->dataHelper->getFileExtension()): null;
    }
}
