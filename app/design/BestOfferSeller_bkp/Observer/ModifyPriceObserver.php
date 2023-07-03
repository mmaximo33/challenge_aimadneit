<?php
namespace Tm\BestOfferSeller\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Tm\BestOfferSeller\Setup\UpgradeSchema;
class ModifyPriceObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $product = $item->getProduct();

        $tm_bestofferseller_offer = $product->getData(UpgradeSchema::COLUMN_NAME) ;

        $item->setData(UpgradeSchema::COLUMN_NAME, json_encode($tm_bestofferseller_offer));

        return $this;
    }
}
