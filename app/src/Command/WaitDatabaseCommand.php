<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class WaitDatabaseCommand.
 */
class WaitDatabaseCommand extends Command
{
    /**
     * @var int
     */
    private const WAIT_SLEEP_TIME = 2;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * WaitDatabaseCommand constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('db:wait')
            ->setDescription('Waits for database availability.')
            ->setHelp('This command allows you to wait for database availability.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        for ($i = 0; $i < 60; $i += self::WAIT_SLEEP_TIME) {
            try {
                $connection = $this->em->getConnection();
                $statement = $connection->prepare('SHOW TABLES');
                $statement->execute();
                $io->success('Connection to the database is OK.');

                return 0;
            } catch (\Exception $e) {
                $output->writeln('Trying to connect to the database... Elapsed:' . $i . 's');
                sleep(self::WAIT_SLEEP_TIME);

                continue;
            }

        }

        throw new \RuntimeException('Cannot connect to the database.');
    }
}
