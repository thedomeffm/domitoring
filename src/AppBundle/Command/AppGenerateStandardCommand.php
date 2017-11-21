<?php

namespace AppBundle\Command;

use AppBundle\Entity\Status;
use AppBundle\Entity\TestStage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppGenerateStandardCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:generate:standard')
            ->setDescription('DEPRECATED!')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $ent1 = new Status();
        $ent1->setName('Elvis - Live');
        $ent1->setUrl('https://elvis.adticket.de/login');
        $em->persist($ent1);

        $test = new TestStage();
        $test->setName('Test');
        $test->setFree(true);
        $em->persist($test);

        $stage = new TestStage();
        $stage->setName('Stage');
        $stage->setFree(true);
        $em->persist($stage);

        $em->flush();
    }

}
