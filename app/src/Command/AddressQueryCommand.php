<?php

namespace App\Command;

use App\Interface\BlockcypherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:address-query',
    description: 'Add a short description for your command',
)]
class AddressQueryCommand extends Command
{
    private BlockcypherInterface $blockcypher;

    public function __construct(BlockcypherInterface $blockcypher)
    {
        parent::__construct();
        $this->blockcypher = $blockcypher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('asset', InputArgument::REQUIRED, 'asset (BTC ot ETH)')
            ->addArgument('address', InputArgument::REQUIRED, 'Address')
            ->addArgument('after', InputArgument::REQUIRED, 'Timeframe: Date from (Y-m-d)')
            ->addArgument('before', InputArgument::REQUIRED, 'Timeframe: Date to (Y-m-d)')
            ->addArgument('threshold', InputArgument::REQUIRED, 'Threshold')

        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $asset = strtolower($input->getArgument('asset'));
        $address = $input->getArgument('address');
        $after = new \DateTime($input->getArgument('after'));
        $before = new \DateTime($input->getArgument('before'));
        $threshold = $input->getArgument('threshold');

        $data = $this->blockcypher->getData($asset, $address, $after, $before, $threshold);



        $table = new Table($output);
        $table
            ->setHeaders(['ASSET', 'ADDRESS', 'DATE FROM', 'DATE TO', 'THRESHOLD'])
            ->setRows([
                [$asset, $address, $input->getArgument('after'), $input->getArgument('before'), $threshold],
            ])
        ;
        $table->render();


        $io->success([
            $data['transactionCount'] . " Tx",
            $data['averageTransactionQuantity'] . " avg. quantity",
        ]);

        return Command::SUCCESS;
    }
}
