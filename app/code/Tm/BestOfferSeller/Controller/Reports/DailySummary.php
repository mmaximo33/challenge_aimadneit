<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Controller\Reports;

class DailySummary extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;
    /**
     * @var \Tm\BestOfferSeller\Console\File\BuildCsv
     */
    private $buildCsv;
    /**
     * @var \Tm\BestOfferSeller\Model\Reports
     */
    private $reports;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Tm\BestOfferSeller\Console\File\BuildCsv $buildCsv
     * @param \Tm\BestOfferSeller\Model\Reports $reports
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Tm\BestOfferSeller\Console\File\BuildCsv $buildCsv,
        \Tm\BestOfferSeller\Model\Reports $reports
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->fileFactory = $fileFactory;
        $this->buildCsv = $buildCsv;
        $this->reports = $reports;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try{

            $date = $this->getRequest()->getParam('date');

            if(!isset($date) || $date === null ){
                throw new \Exception('You should add /date/2023-07-01');
            }
            $reportDate = $this->reports->reportCsv($date);

            $result = $this->buildCsv->generateCSV($date,$reportDate);
            if(!isset($result) || $result['status'] === false ){
                throw new \Exception('CSV file creation failed');
            }
            $filePath = $result['file'];
            $fileName = 'report.csv';

            $file = [
                'type' => 'filename',
                'value' => $filePath,
                'rm' => true
            ];

            $response = $this->fileFactory->create(
                $fileName,
                $file,
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
                'application/octet-stream',
                null,
                true
            );
        }catch (\Exception $e){
            $result = $this->resultJsonFactory->create();

            return $result
                ->setHttpResponseCode(500)
                ->setData(['error' => $e->getMessage()]);
        }
    }
}
