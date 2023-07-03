<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\Reports;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Tm\BestOfferSeller\Model\Reports $reports
    )
    {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->reports = $reports;
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $date = $this->getRequest()->getParam('date');
            $sku = $this->getRequest()->getParam('sku');

            if(isset($date) && $date !== null ){
                return $result
                    ->setHttpResponseCode(200)
                    ->setData($this->reports->getOrderByDateWithBestOffers($date));
            }

            if(isset($sku) && $sku !== null ){
                return $result
                    ->setHttpResponseCode(200)
                    ->setData($this->reports->getOrderBySkuWithBestOffers($sku));
            }
        } catch (\Exception $e) {
            return $result
                ->setHttpResponseCode(500)
                ->setData(['error' => $e->getMessage()]);
        }

        return $result
            ->setHttpResponseCode(500)
            ->setData(['checkParam' => 'Use sku/24-MB01 or date/2023-07-01']);
    }
}
