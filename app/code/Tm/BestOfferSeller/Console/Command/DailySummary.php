<?php declare(strict_types=1);

namespace Tm\BestOfferSeller\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DailySummary extends Command
{
    private const ARG_DATE = 'date';
    /**
     * @var \Tm\BestOfferSeller\Console\File\BuildCsv
     */
    private $buildCsv;
    private \Tm\BestOfferSeller\Model\Reports $reports;

    public function __construct(
        \Tm\BestOfferSeller\Console\File\BuildCsv $buildCsv,
        \Tm\BestOfferSeller\Model\Reports $reports,
        string $name = null)
    {
        parent::__construct($name);
        $this->buildCsv = $buildCsv;
        $this->reports = $reports;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('tmbestofferseller:dailysummary')
            ->setDescription('generate a daily report in excel /var/report/besofferseller_sumary.')
            ->addArgument(
                self::ARG_DATE,
                InputArgument::REQUIRED,
                'Date order in format YYYY-MM-DD'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $date = $input->getArgument(self::ARG_DATE);
        try{
            $reportDate = $this->reports->reportCsv($date);
            if($reportDate === null || empty($reportDate)){
                $msg = sprintf("No records found for the mentioned date %s", $date);
                $output->writeln('<options=bold;fg=red>' . $msg . '</>');
                return 1;
            }

            $this->buildCsv->generateCSV($date,$reportDate);

            $output->writeln('<options=bold;fg=green>Report created successfully.</>');
            return 0;
        }catch (\Exception $e){
            $output->writeln('<error>' . $e . '</error>');
        }

    }
}
