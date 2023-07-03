<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\BestOffer;

use Tm\Provider\Controller\Provider\Get as XXXGET;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Tm\BestOfferSeller\Model\BestOfferFinder
     */
    private $bestOfferFinder;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Tm\BestOfferSeller\Model\BestOfferFinder $bestOfferFinder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Tm\BestOfferSeller\Model\BestOfferFinder $bestOfferFinder,
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->bestOfferFinder = $bestOfferFinder;

        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
//        $result = $this->resultJsonFactory->create();
//        $data = $this->bestOfferFinder->getBestOffer(
//            $this->getRequest()->getParam('sku'),
//            100
//        );
//
//        return $result
//            ->setHttpResponseCode(200)
//            ->setData($data);
    }
}
