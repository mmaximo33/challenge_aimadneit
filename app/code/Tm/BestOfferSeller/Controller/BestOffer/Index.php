<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\BestOffer;

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
        try {
            $result = $this->resultJsonFactory->create();

            $sku = $this->getRequest()->getParam('sku');

            $data = $this->bestOfferFinder->getBestOffer(
                $sku,
                100 //MMTodo: Agregar producRepository
            );

            return $result
                ->setHttpResponseCode(200)
                ->setData($data);
        } catch (\Exception $e) {
            return $result
                ->setHttpResponseCode(500)
                ->setData(['error' => $e->getMessage()]);
        }
    }
}
