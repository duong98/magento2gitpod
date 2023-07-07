<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magezon\Composer\Controller\Adminhtml\Version;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\Exception;
use Magezon\Composer\Api\Data\VersionInterface;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem\Driver\File;

/**
 * Delete customer address controller action.
 */
class Download extends Action
{
    /**
     * @var File
     */
    protected $fileDriver;
    /**
     * @var VersionRepositoryInterface
     */
    protected $_versionRepository;
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var VersionInterface
     */
    protected $versionModel;
    public function __construct(
        Context $context,
        VersionRepositoryInterface $versionRepository,
        File $fileDriver,
        FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->_versionRepository = $versionRepository;
        $this->fileDriver = $fileDriver;
        parent::__construct($context);
    }

    /**
     * Execute download
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function execute()
    {
        $versionId = $this->getRequest()->getParam('version_id', false);

        return $this->fileDownload($versionId);
    }

    /**
     * Download package
     *
     * @param string $versionId
     * @return ResponseInterface
     * @throws Exception
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function fileDownload(
        string $versionId
    ) {
        $this->versionModel = $this->_versionRepository->getById($versionId);
        $zipData = json_decode($this->versionModel->getData('zip'), true);
        $baseDir = DirectoryList::VAR_DIR;
        $fileFactory = $this->fileFactory;
        $contentType = 'application/octet-stream';
        $packageFile = $zipData['name'];
        $dist = 'packages/files';
        $filepath = BP . DIRECTORY_SEPARATOR . $baseDir. DIRECTORY_SEPARATOR . $dist . DIRECTORY_SEPARATOR . $packageFile;
        if ($packageFile == $this->getRequest()->getParam('name', false)) {
            $content = $this->fileDriver->fileGetContents($filepath);
            return $fileFactory->create($packageFile, $content, $baseDir, $contentType);
        } else {
            throw new LocalizedException(
                __('File name mismatch.')
            );
        }
    }
}
