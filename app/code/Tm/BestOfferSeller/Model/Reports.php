<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Model;

use Tm\BestOfferSeller\Setup\UpgradeSchema;

class Reports
{
    private const FIELD_TABLE_TM_BEST_OFFERS = UpgradeSchema::COLUMN_NAME;
    private const REPORT_ORDERS_BY_SKU_WITH_BEST_OFFERS = 0;
    private const REPORT_ORDERS_BY_DATE_WITH_BEST_OFFERS = 1;

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
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->timezone = $timezone;
    }

    /**
     * @param $sku
     * @return array
     */
    public function getOrderBySkuWithBestOffers($sku){
        $product = $this->productRepository->get($sku);
        $response = $this->getDataReport(
            self::REPORT_ORDERS_BY_SKU_WITH_BEST_OFFERS,
            ['productId' => $product->getId()]
        );
        return $response;
    }

    /**
     * @param $date
     * @return array
     */
    public function getOrderByDateWithBestOffers($date){
        $response = $this->getDataReport(
            self::REPORT_ORDERS_BY_DATE_WITH_BEST_OFFERS,
            ['date' => $date]
        );
        return $response;
    }

    /**
     * @param $type
     * @param $aditional
     * @return array
     */
    private function getDataReport($type, $aditional = []){
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->getSelect()->join(
            ['order_item' => 'sales_order_item'],
            'main_table.entity_id = order_item.order_id',
            ['product_id']
        );

        // Only with BEST OFFER SELLER
        $orderCollection->addFieldToFilter(
            sprintf('order_item.%s', self::FIELD_TABLE_TM_BEST_OFFERS),
            ['notnull' => true]
        );

        switch ($type){
            case self::REPORT_ORDERS_BY_SKU_WITH_BEST_OFFERS:
                $orderCollection->addFieldToFilter(
                    'order_item.product_id',
                    array('eq' => $aditional['productId'])
                );
                break;
            case self::REPORT_ORDERS_BY_DATE_WITH_BEST_OFFERS:
                //MmTodo: Mejorar esto para suprimir el rango
                $date = $aditional['date'];
                $startDate = date('Y-m-d 00:00:00', strtotime($date));
                $endDate = date('Y-m-d 23:59:59', strtotime($date));

                $orderCollection->addFieldToFilter(
                    'order_item.created_at',
                    ['from' => $startDate, 'to' => $endDate]
                );
                break;
            default:
                throw new \Exception('Unexpected value');
        }

        return $this->prepareResponse(
            $orderCollection->getItems()
        );
    }

    /**
     * @param $orders
     * @return array
     */
    private function prepareResponse($orders){
        $orderData = [];
        foreach ($orders as $order) {
            foreach ($order->getItems() as $item) {
                // BASIC
                $row = [
                    'sku' => $item->getSku(),
                    'type' => $item->getProductType(),
                    'order_id' => $order->getIncrementId(),
                    'customer_name' => $order->getCustomerName(),
                    'state' => $order->getState(),
                    'status' => $order->getStatus(),
                    'createdAt' => $this->dateTimeFormat($order->getCreatedAt()),
                    'updatedAt' => $this->dateTimeFormat($order->getUpdatedAt()),
                ];
                $bestOfferSellerData = $item->getData(self::FIELD_TABLE_TM_BEST_OFFERS);
                if (!empty($bestOfferSellerData)){
                    $bestOfferSellerJson = json_decode($bestOfferSellerData,true);
                    if ($bestOfferSellerJson !== null && json_last_error() === JSON_ERROR_NONE) {
                        $row['bestOfferSeller'] = $bestOfferSellerJson;
                    }
                }

                $orderData[] = $row;
            }
        }
        return $orderData;
    }

    /**
     * @param $datetime
     * @param $format
     * @return mixed
     */
    private function dateTimeFormat($datetime, $format = 'yyyy-MM-dd HH:mm'){
        return $this->timezone->formatDateTime(
            $datetime,
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::SHORT,
            null,
            null,
            $format);
    }

    /**
     * @param $date
     * @return array
     */
    public function reportCsv($date){
        $reportDate = $this->getOrderByDateWithBestOffers($date);
        $data[] = ['sku','type','order_id','createdAt','state','status','bestOfferSeller'];

        foreach ($reportDate as $row){
            $data[] = [
                $row['sku'],
                $row['type'],
                $row['order_id'],
                $row['createdAt'],
                $row['state'],
                $row['status'],
                json_encode($row['bestOfferSeller']),
            ];
        }

        return $data;
    }
}
