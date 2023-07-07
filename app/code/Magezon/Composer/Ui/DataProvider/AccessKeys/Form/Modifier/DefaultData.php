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

namespace Magezon\Composer\Ui\DataProvider\AccessKeys\Form\Modifier;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DefaultData implements ModifierInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
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
     * @param CustomerRepositoryInterface $customerRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        CustomerRepositoryInterface $customerRepository,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     *
     * @throws LocalizedException
     */
    public function modifyData(array $data)
    {
        $data[null]['customer_button_label'] = __('Choose Customer');
        $data[null]['customer_edit_url'] = $this->urlBuilder->getUrl('customer/index/edit');
        $data[null]['entity_id'] = $this->request->getParam('entity_id');

        foreach ($data as & $accessKeysData) {
            // fix customer_field null
            if (empty($accessKeysData['entity_id'])) {
                $customerButtonLabel  = __('Choose Customer');
            } else {
                try {
                    $customer = $this->customerRepository->getById($accessKeysData['entity_id']);
                    $accessKeysData['customer_label'] = $customer->getFirstname() . ' (' . $customer->getEmail() . ')';
                    $accessKeysData['customer_link']
                        = $this->urlBuilder->getUrl('customer/index/edit', ['id' => $customer->getId()]);
                    $customerButtonLabel = __('Change Customer');
                } catch (NoSuchEntityException $e) {
                    $accessKeysData['entity_id'] = null;
                    $customerButtonLabel  = __('Choose Customer');
                }
            }

            $accessKeysData['customer_edit_url'] = $this->urlBuilder->getUrl('customer/index/edit');
            $accessKeysData['customer_button_label'] = $customerButtonLabel;
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
