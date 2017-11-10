<?php

namespace AppBundle\Command;

use AppBundle\Entity\Status;
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
            ->setDescription('...')
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
        /*
        $ent2 = new Status();
        $ent2->setName('Elvis - Test');
        $ent2->setUrl('https://test-elvis.adticket.de/login');
        $em->persist($ent2);
        */
        /*
        $ent3 = new Status();
        $ent3->setName('Elvis - Stage');
        $ent3->setUrl('https://stage-elvis.adticket.de/login');
        $em->persist($ent3);
        */

        $em->flush();
    }

}
