<?php declare(strict_types=1);

namespace Tm\Provider\Controller\Provider;

use Tm\Provider\Model\Configuration\Config;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var string
     */
    private $currentUrl;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Tm\Provider\Model\DataBase
     */
    private $randomSuccess;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Tm\Provider\Model\DataBase $randomSuccess
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Tm\Provider\Model\DataBase $randomSuccess
    ) {
        $this->currentUrl = $urlInterface->getCurrentUrl();
        $this->resultJsonFactory = $resultJsonFactory;
        $this->randomSuccess= $randomSuccess->getDataBase(true);
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $randomSuccessRate = $this->randomSuccess['rate'];
        $dataBase = $this->randomSuccess['database'];
        $prefixSku = '24-MB0';

        return $result
            ->setHttpResponseCode(200)
            ->setData([
                'message' => __('tm_provider_endpoint_200'),
                'usaged' => [
                    'settings' => [
                        'path' => Config::SETTING_SUCCESS_RATE,
                        'value' => sprintf("%s%%", $randomSuccessRate)
                    ],
                    'entpoints' => [
                        'all' => sprintf('%stm_provider/Prodiver/All', $this->currentUrl),
                        'sku' => sprintf('%stm_provider/Prodiver/get/sku/%s', $this->currentUrl, '24-MB01'),
                        'skus_test' => implode(', ',
                            array_map(function ($num) use ($prefixSku) { return $prefixSku . $num; }, range(1, 5))
                        )
                    ],
                    'errors' => [
                        '200' => __('tm_provider_endpoint_200'),
                        '202' => __('tm_provider_endpoint_202'),
                        '400' => __('tm_provider_endpoint_400'),
                        '500' => __('tm_provider_endpoint_500'),
                    ]
                ],
                'data_sample' => $dataBase
            ]);
    }
}
