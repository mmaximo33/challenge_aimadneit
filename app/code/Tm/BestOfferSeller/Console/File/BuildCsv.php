<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Console\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class BuildCsv
{
    private const FILE_NAME = 'bestoffersellers_orders.csv';
    private $filesystem;
    private $directoryList;
    private $csvProcessor;

    /**
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\File\Csv                     $csvProcessor,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem                   $filesystem
    )
    {
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * @param $data
     * @return true
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function generateCSV($date, $data = [])
    {
        try{
            $fileDirectoryPath = $this->directoryList
                    ->getPath(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR ) . "/BestOfferSeller/";

            if (!is_dir($fileDirectoryPath)){
                mkdir($fileDirectoryPath, 0777, true);
            }

            $fileName = sprintf("%s-%s",
                $date,
                self::FILE_NAME);
            $filePath = $fileDirectoryPath . '/' . $fileName;

            $this->csvProcessor
                ->setEnclosure('"')
                ->setDelimiter(',')
                ->saveData($filePath, $data);

            return [
                'status' => true,
                'file'=>$filePath
            ];
        }catch (\Exception $e){
            return [
                'status' => false,
                'file'=>$e
            ];
        }
    }
}
