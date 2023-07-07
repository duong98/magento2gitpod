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
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

namespace Magezon\Composer\Controller\Packages;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magezon\Composer\Controller\Packages;

class Version extends Packages implements HttpGetActionInterface
{
    /**
     * Execute Render Page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Package Version Information'));
        $resultPage->getConfig()->setDescription('Version Information');
        $packageId = $this->_versionRepository->getById($this->getRequest()->getParam('version_id'))
            ->getData('package_id');
        $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'packages',
            [
                'label' => __('Packages'),
                'title' => __('Packages'),
                'link' => $this->_url->getUrl('composer/packages')
            ]
        );
        $breadcrumbs->addCrumb(
            'versions',
            [
                'label' => __('Versions'),
                'title' => __('Versions'),
                'link' => $this->_url->getUrl('composer/packages/versions', ['package_id' => $packageId])
            ]
        );
        $breadcrumbs->addCrumb(
            'version',
            [
                'label' => __('Version'),
                'title' => __('Version')
            ]
        );
        return $resultPage;
    }
}
