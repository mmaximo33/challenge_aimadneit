<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Block\Product;

use Tm\BestOfferSeller\Setup\UpgradeSchema;
use Magento\Catalog\Block\Product\View as ProductView;

class BestOfferSellerOldPrice extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @return bool
     */
    public function hasCustomAttribute()
    {
        return $this->getProduct()->getData(UpgradeSchema::COLUMN_NAME) !== null; // Reemplaza "getCustomAttribute" con el método apropiado para obtener el atributo deseado
    }

    /**
     * @return mixed|null
     */
    public function getCustomAttribute()
    {
        return $this->getProduct()->getData(UpgradeSchema::COLUMN_NAME); // Reemplaza "getCustomAttribute" con el método apropiado para obtener el atributo deseado
    }

    public function getOldPrice(){
        $bestoffersellers = $this->hasCustomAttribute();
        if($bestoffersellers !== null){
           return number_format(
               floatval(
                   $this->getCustomAttribute()['originalPrice']
               ),
               2);
        }
        return '';
    }

}
