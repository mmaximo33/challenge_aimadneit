<?php declare(strict_types=1);
namespace Tm\BestOfferSeller\Observer\Sales;

use Magento\Framework\Logger\Monolog;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Tm\BestOfferSeller\Setup\UpgradeSchema;
use Tm\BestOfferSeller\Model\TmBestOfferSellerOrdersFactory AS TBOSOModel;
use Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders AS TBOSOResoruce;

class OrderItemSaveAfter implements ObserverInterface
{
    private const TBOSO_FIELD_NAME = UpgradeSchema::COLUMN_NAME;

    private Monolog $logger;
    private TBOSOModel $tbosoModel;
    private TBOSOResoruce $tbosoResource;

    /**
     * @param Monolog $logger
     * @param TBOSOModel $tbosoModel
     * @param TBOSOResoruce $tbosoResource
     */
    public function __construct(
        Monolog $logger,
        TBOSOModel $tbosoModel,
        TBOSOResoruce $tbosoResource
    ){
        $this->logger = $logger;
        $this->tbosoModel = $tbosoModel;
        $this->tbosoResource = $tbosoResource;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {

        try {
            $orderItem = $observer->getEvent()->getItem();
            if($orderItem->getData(self::TBOSO_FIELD_NAME) !== null){
                $this->registerData($orderItem);
            }
        }catch (\Exception){

        }
        return $this;
    }

    /**
     * @param $orderItem
     * @return void
     */
    private function registerData($orderItem): void
    {
        try{
            $newRow = $this->tbosoModel
                ->create()
                ->setSku($orderItem->getSku())
                ->setOrderId(intval($orderItem->getOrderId()))
                ->setOrderItemId(intval($orderItem->getId()))
                ->setBestOfferSeller(
                    $orderItem->getData(self::TBOSO_FIELD_NAME)
                );
            $this->tbosoResource->save($newRow);
        }catch (\Exception $e){
            $this->logger->error('BestOfferSeller', $orderItem);
        }
    }
}
