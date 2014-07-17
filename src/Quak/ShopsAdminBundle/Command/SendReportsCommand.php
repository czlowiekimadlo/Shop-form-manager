<?php
namespace Quak\ShopsAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for sending reports
 */
class SendReportsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('reports:send')
            ->setDescription('Send emails with reports');
    }

    /**
     * {@inheritDoc}
     *
     * @param InputInterface  $input  input parameters
     * @param OutputInterface $output output writer
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('reporter')->runSchedule();
    }
}