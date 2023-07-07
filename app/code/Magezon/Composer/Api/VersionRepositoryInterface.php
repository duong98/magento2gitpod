<?php
/**
 * Copyright © 2018 EaDesign by Eco Active S.R.L. All rights reserved.
 * See LICENSE for license details.
 */

namespace Magezon\Composer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magezon\Composer\Api\Data\VersionInterface;

interface VersionRepositoryInterface
{
    /**
     * Save version
     *
     * @param VersionInterface $templates
     * @return mixed
     */
    public function save(VersionInterface $templates);

    /**
     * Save versions by data
     *
     * @param string $name
     * @param string $versionOrigin
     * @param string $versionNr
     * @param string $distUrl
     * @param string $reference
     * @return mixed
     */
    public function saveVersions($name, $versionOrigin, $versionNr, $distUrl, $reference);

    /**
     * Get version by id
     *
     * @param mixed $value
     * @return VersionInterface
     */
    public function getById($value);

    /**
     * Delete versions
     *
     * @param VersionInterface $templates
     * @return mixed
     */
    public function delete(VersionInterface $templates);

    /**
     * Delete version by id
     *
     * @param mixed $value
     * @return mixed
     */
    public function deleteById($value);

    /**
     * Get version list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
