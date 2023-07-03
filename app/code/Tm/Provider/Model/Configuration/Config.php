<?php declare(strict_types=1);
namespace Tm\Provider\Model\Configuration;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const SETTING_SUCCESS_RATE = "tm_provider/general/success_rate";

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * Construct
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Success Rate
     *
     * @return null|string
     */
    public function getSuccessRate()
    {
        return $this->scopeConfig->getValue(
            self::SETTING_SUCCESS_RATE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
