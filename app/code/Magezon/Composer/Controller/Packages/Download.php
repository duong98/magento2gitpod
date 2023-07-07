<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magezon\Composer\Controller\Packages;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Validator\Exception;
use Magezon\Composer\Api\Data\VersionInterface;
use Magezon\Composer\Controller\Packages;

/**
 * Delete customer address controller action.
 */
class Download extends Packages implements HttpPostActionInterface, HttpGetActionInterface
{

    /**
     * @var VersionInterface
     */
    protected $versionModel;

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
     * @throws \Exception
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
        $filepath = BP . DIRECTORY_SEPARATOR . $baseDir . DIRECTORY_SEPARATOR . $dist . DIRECTORY_SEPARATOR . $packageFile;
        $content = $this->fileDriver->fileGetContents($filepath);
        return $fileFactory->create($packageFile, $content, $baseDir, $contentType);
    }
}
