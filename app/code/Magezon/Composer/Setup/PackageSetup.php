<?php

namespace Magezon\Composer\Setup;

use Magezon\Composer\Model\PackagesFactory as ModelPackagesFactory;
class PackageSetup
{
    protected $packagesFactory;
    public function __construct(
        ModelPackagesFactory $packagesFactory
    ) {
        $this->packagesFactory = $packagesFactory;
    }

    public function createPackage($data = [])
    {
        return $this->packagesFactory->create($data);
    }
}