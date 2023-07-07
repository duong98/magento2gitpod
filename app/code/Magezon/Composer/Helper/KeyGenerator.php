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

namespace Magezon\Composer\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;

class KeyGenerator extends AbstractHelper
{
    /**
     * Generate key string
     *
     * @return string
     * @throws Exception
     */
    public function generateKey(): string
    {
        $key = random_bytes(17);
        return bin2hex($key);
    }

    /**
     * Generate secret string
     *
     * @return string
     * @throws Exception
     */
    public function generateSecret(): string
    {
        $secret = random_bytes(17);
        return bin2hex($secret);
    }
}
