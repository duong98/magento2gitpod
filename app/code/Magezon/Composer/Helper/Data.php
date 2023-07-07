<?php

namespace Magezon\Composer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\File\Size;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{

    const XML_PATH_COMPOSER = 'composer/';

    /**
     * @var Size
     */
    protected $fileSize;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param Size $fileSize
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->fileSize = $fileSize;
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getConfig($code, $store = null)
    {
        $storeId = $this->storeManager->getStore($store);
        return $this->getConfigValue(self::XML_PATH_COMPOSER . $code, $storeId);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getFileExtension()
    {
        return $this->getConfig('general/extensions');
    }
    /**
     * Get max file size in megabytes
     *
     * @param int $precision
     * @param int $mode
     * @return float
     */
    public function getMaxFileSizeInMb($precision = 0, $mode = \PHP_ROUND_HALF_DOWN)
    {
        return $this->fileSize->getFileSizeInMb($this->getMaxFileSize(), $precision, $mode);
    }

    /**
     * Get maximum upload size message
     *
     * @return \Magento\Framework\Phrase
     */
    public function getMaxUploadSizeMessage()
    {
        $maxImageSize = $this->getMaxFileSizeInMb();
        if ($maxImageSize) {
            $message = __('Make sure your file isn\'t more than %1M.', $maxImageSize);
        } else {
            $message = __('We can\'t provide the upload settings right now.');
        }
        return $message;
    }

    /**
     * Get the maximum file size of the a form in bytes
     *
     * @return integer
     */
    public function getMaxFileSize()
    {
        return $this->fileSize->getMaxFileSize();
    }

    /// magento/framework/File/Mime.php
    public function getMimeTypes()
    {
        return [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
        ];
    }
}