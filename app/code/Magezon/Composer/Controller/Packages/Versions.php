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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\View\Result\Page;
use Magezon\Composer\Controller\Packages;

class Versions extends Packages implements HttpGetActionInterface
{
    /**
     * Protect Access Keys routes
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws SessionException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate() || !$request->getParam('package_id')) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
    /**
     * Execute Render Page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Package Versions'));
        $resultPage->getConfig()->setDescription('Versions Table');
        // Add breadcrumb
        /** @var \Magento\Theme\Block\Html\Breadcrumbs */
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
                'link' => $this->_url->getUrl('composer/packages/versions')
            ]
        );
        return $resultPage;
    }
}
