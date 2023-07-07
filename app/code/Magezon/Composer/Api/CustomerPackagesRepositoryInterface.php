<?php
namespace Magezon\Composer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magezon\Composer\Api\Data\CustomerPackagesInterface;

interface CustomerPackagesRepositoryInterface
{
    /**
     * Save package
     *
     * @param CustomerPackagesInterface $templates
     * @return mixed
     */
    public function save(CustomerPackagesInterface $templates);

    /**
     * Get by id
     *
     * @param string $value
     * @return mixed
     */
    public function getById($value);

    /**
     * Delele package
     *
     * @param CustomerPackagesInterface $templates
     * @return mixed
     */
    public function delete(CustomerPackagesInterface $templates);

    /**
     * Delete by id
     *
     * @param string $value
     * @return mixed
     */
    public function deleteById($value);

    /**
     * Get list
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Get authenticated packages list by customer id.
     *
     * @param string $auth
     * @param string $secret
     * @return mixed
     */
    public function getAuthListByKeys(string $auth, string $secret);

    /**
     * Check if api keys is validated.
     *
     * @param string $auth
     * @param string $secret
     * @param string $pName
     * @return bool
     */
    public function checkAuth(string $auth, string $secret, string $pName);
    /**
     * Get which package belong to which customer collection
     *
     * @return mixed
     */
    public function getCustomerPackagesCollection();

    /**
     * Join with Sales Collection by package name
     *
     * @return mixed
     */
    public function getSalesCollection();

    /**
     * Get authenticated packages list by customer id.
     *
     * @param string $customer_id
     * @return mixed
     */
    public function getAuthList(string $customer_id);
}
