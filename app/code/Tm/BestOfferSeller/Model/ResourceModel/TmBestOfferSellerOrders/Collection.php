<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Tm\BestOfferSeller\Model\TmBestOfferSellerOrders::class,
            \Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders::class
        );
    }
}
