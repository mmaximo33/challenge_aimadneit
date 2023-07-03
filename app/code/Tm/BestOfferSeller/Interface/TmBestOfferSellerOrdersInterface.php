<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Interface;

use Tm\BestOfferSeller\Model\TmBestOfferSellerOrders;

interface TmBestOfferSellerOrdersInterface
{
    const ENTITY_ID = 'entity_id';

    const SKU = 'sku';
    const ORDER_ID = 'order_id';
    const ORDER_ITEM_ID = 'order_item_id';

    const BEST_OFFER_SELLER = 'best_offer_seller';

    const CREATED_AT = 'created_at';

    /**
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * @param string $sku
     * @return TmBestOfferSellerOrders
     */
    public function setSku(string $sku): TmBestOfferSellerOrdersInterface;

    /**
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * @param int $orderId
     * @return TmBestOfferSellerOrdersInterface
     */
    public function setOrderId(int $orderId): TmBestOfferSellerOrdersInterface;

    /**
     * @return int|null
     */
    public function getOrderItemId(): ?int;

    /**
     * @param int $orderItemId
     * @return TmBestOfferSellerOrdersInterface
     */
    public function setOrderItemId(int $orderItemId): TmBestOfferSellerOrdersInterface;

    /**
     * @return string|null
     */
    public function getBestOfferSeller(): ?string;

    /**
     * @param string $BestOfferSeller
     * @return TmBestOfferSellerOrdersInterface
     */
    public function setBestOfferSeller(string $BestOfferSeller): TmBestOfferSellerOrdersInterface;


    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

}
