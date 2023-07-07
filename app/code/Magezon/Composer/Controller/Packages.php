<?php

namespace Magezon\Composer\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\Data\PackagesInterfaceFactory;
use Magezon\Composer\Api\PackagesRepositoryInterface;
use Magezon\Composer\Api\VersionRepositoryInterface;
use Psr\Log\LoggerInterface;

abstract class Packages extends Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var File
     */
    protected $fileDriver;

    /**
     * @var Validator
     */
    protected $_formKeyValidator;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /** @var PackagesInterfaceFactory $accessKeysFactory */
    protected $_packagesFactory;

    /**
     * @var PackagesRepositoryInterface
     */
    protected $_packagesRepository;
    /**
     * @var VersionRepositoryInterface
     */
    protected $_versionRepository;
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Constructor abstract crud class
     *
     * @param Context $context
     * @param Session $customerSession
     * @param Validator $formKeyValidator
     * @param PackagesInterfaceFactory $packagesFactory
     * @param PackagesRepositoryInterface $packagesRepository
     * @param VersionRepositoryInterface $versionRepository
     * @param LoggerInterface $logger
     * @param File $fileDriver
     * @param FileFactory $fileFactory
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Validator $formKeyValidator,
        PackagesInterfaceFactory $packagesFactory,
        PackagesRepositoryInterface $packagesRepository,
        VersionRepositoryInterface $versionRepository,
        LoggerInterface $logger,
        File $fileDriver,
        FileFactory $fileFactory,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_logger = $logger;
        $this->fileFactory = $fileFactory;
        $this->_packagesFactory = $packagesFactory;
        $this->_packagesRepository = $packagesRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_versionRepository = $versionRepository;
        $this->fileDriver = $fileDriver;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session object
     *
     * @return Session
     */
    protected function _getSession(): Session
    {
        return $this->_customerSession;
    }

    /**
     * Build Url class
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function _buildUrl(string $route = '', array $params = []): string
    {
        /** @var UrlInterface $urlBuilder */
        $urlBuilder = $this->_objectManager->create(UrlInterface::class);
        return $urlBuilder->getUrl($route, $params);
    }

    /**
     * Protect Packages routes
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws SessionException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
//            || (!$request->getServer('PHP_AUTH_USER') || !$request->getServer('PHP_AUTH_PW'))) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
}
