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

namespace Magezon\Composer\Ui\DataProvider\AccessKeys\Form\Customer;

use Magento\Customer\Ui\Component\DataProvider as CustomerDataProvider;

class DataProvider extends CustomerDataProvider
{
    /**
     * Get Customer Collection
     *
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        return $data;
    }
}
