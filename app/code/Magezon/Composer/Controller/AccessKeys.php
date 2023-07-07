<?php

namespace Magezon\Composer\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;
use Magezon\Composer\Api\AccessKeysRepositoryInterface;
use Magezon\Composer\Api\Data\AccessKeysInterfaceFactory;
use Psr\Log\LoggerInterface;

abstract class AccessKeys extends Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Validator
     */
    protected $_formKeyValidator;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /** @var AccessKeysInterfaceFactory $accessKeysFactory */
    protected $_accessKeysFactory;

    /**
     * @var AccessKeysRepositoryInterface
     */
    protected $_accessKeysRepository;
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor abstract crud class
     *
     * @param Context $context
     * @param Session $customerSession
     * @param Validator $formKeyValidator
     * @param AccessKeysInterfaceFactory $accessKeysFactory
     * @param AccessKeysRepositoryInterface $accessKeysRepository
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Validator $formKeyValidator,
        AccessKeysInterfaceFactory $accessKeysFactory,
        AccessKeysRepositoryInterface $accessKeysRepository,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_logger = $logger;
        $this->_accessKeysFactory = $accessKeysFactory;
        $this->_accessKeysRepository = $accessKeysRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
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
     * Protect Access Keys routes
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws SessionException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
}
