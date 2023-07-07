<?php
namespace Magezon\Composer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magezon\Composer\Api\Data\PackagesInterface;

interface PackagesRepositoryInterface
{
    /**
     * Save package
     *
     * @param PackagesInterface $templates
     * @return mixed
     */
    public function save(PackagesInterface $templates);

    /**
     * Get by id
     *
     * @param string $sku
     * @return PackagesInterface
     */
    public function getById($sku);

    /**
     * Get by sku
     *
     * @param string $value
     * @return PackagesInterface
     */
    public function getBySku($value);

    /**
     * Get by package name
     *
     * @param string $value
     * @return PackagesInterface
     */
    public function getByPackageName($value);

    /**
     * Delele package
     *
     * @param PackagesInterface $templates
     * @return mixed
     */
    public function delete(PackagesInterface $templates);

    /**
     * Delete by id
     *
     * @param string $value
     * @return mixed
     */
    public function deleteById($value);

    /**
     * Update package latest version
     *
     * @param string $name
     * @param string $version
     * @param string $package_json
     * @return mixed
     */
    public function updatePackageVersion($name, $version, $package_json);

    /**
     * Get list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
