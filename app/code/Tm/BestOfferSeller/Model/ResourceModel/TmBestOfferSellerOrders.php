<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TmBestOfferSellerOrders extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tm_bestofferseller_orders', 'entity_id');
    }
}
