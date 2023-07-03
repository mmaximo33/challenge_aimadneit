<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Model;

use Magento\Framework\Model\AbstractModel;
use Tm\BestOfferSeller\Interface\TmBestOfferSellerOrdersInterface;

class TmBestOfferSellerOrders extends AbstractModel implements TmBestOfferSellerOrdersInterface
{
    /**
     * @return void
     */
    protected function _construct(){
        $this->_init(\Tm\BestOfferSeller\Model\ResourceModel\TmBestOfferSellerOrders::class);
    }


    /**
     * @return int|null
     */
    public function getEntityId(): ?int
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->getData(self::SKU);
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku(string $sku): TmBestOfferSellerOrdersInterface
    {
        $this->setData(self::SKU, $sku);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderId(): ?int
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return TmBestOfferSellersInterface
     */
    public function setOrderId(int $orderId): TmBestOfferSellerOrdersInterface
    {
        $this->setData(self::ORDER_ID, $orderId);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderItemId(): ?int
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @param int $orderItemId
     * @return TmBestOfferSellersInterface
     */
    public function setOrderItemId(int $orderItemId): TmBestOfferSellerOrdersInterface
    {
        $this->setData(self::ORDER_ITEM_ID, $orderItemId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBestOfferSeller(): ?string
    {
        return $this->getData(self::BEST_OFFER_SELLER);
    }

    /**
     * @param string $BestOfferSeller
     * @return TmBestOfferSellersInterface
     */
    public function setBestOfferSeller(string $BestOfferSeller): TmBestOfferSellerOrdersInterface
    {
        $this->setData(self::BEST_OFFER_SELLER, $BestOfferSeller);
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt():  ?\DateTime
    {
        $dateStr = $this->getData(self::CREATED_AT);
        try {
            $dateObject = ($dateStr) ? new \DateTime($dateStr) : null;
        } catch (\Exception $e) {
            $dateObject = null;
        }
        return $dateObject;
    }


}
