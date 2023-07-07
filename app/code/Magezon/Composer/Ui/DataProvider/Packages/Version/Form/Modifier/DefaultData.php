<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Composer
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

declare(strict_types=1);

namespace Magezon\Composer\Ui\DataProvider\Packages\Version\Form\Modifier;

use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DefaultData implements ModifierInterface
{
    /**
     * @var PackagesRepositoryInterface
     */
    protected $packagesRepository;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * DefaultData constructor.
     *
     * @param RequestInterface $request
     * @param PackagesRepositoryInterface $packagesRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        PackagesRepositoryInterface $packagesRepository,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->packagesRepository = $packagesRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function modifyData(array $data)
    {
        $data[null]['package_button_label'] = __('Choose Package');
        $data[null]['package_edit_url'] = $this->urlBuilder->getUrl('composer/packages/edit');
        $data[null]['package_id'] = $this->request->getParam('package_id');

        foreach ($data as & $versionData) {
            // fix pacakge_field null
            if (empty($versionData['package_id'])) {
                $packageButtonLabel  = __('Choose Package');
            } else {
                try {
                    $package = $this->packagesRepository->getById($versionData['package_id']);
                    $versionData['package_label'] = $package->getName();
                    $versionData['package_link']
                        = $this->urlBuilder->getUrl('composer/packages/edit', ['id' => $package->getId()]);
                    $packageButtonLabel = __('Change Package');
                } catch (NoSuchEntityException $e) {
                    $versionData['package_id'] = null;
                    $packageButtonLabel  = __('Choose Package');
                }
            }

            $versionData['package_edit_url'] = $this->urlBuilder->getUrl('composer/packages/edit');
            $versionData['package_button_label'] = $packageButtonLabel;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
