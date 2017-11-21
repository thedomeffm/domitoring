<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServerPingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ServerPing')
            ->setDescription('Get the current state of the servers')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $serverPingRepo = $doctrine->getRepository('AppBundle:ServerPing');
        $allServerPing = $serverPingRepo->findAll();

        if ($allServerPing == null)
        {
            $output->writeln('<error>No entries to check!</error>');
            return false;
        }

        foreach ($allServerPing as $serverPing)
        {
            //get httpCode
            $httpCode = $this->getHttpCode($serverPing->getUrl());

            //set the states for live requests
            $serverPing->setPingDatetime(new \DateTime());
            $serverPing->setPingHttpCode($httpCode);
            //true == reachable
            if($httpCode>=200 && $httpCode<300){
                $serverPing->setPingStatus(true);
            }
            //false == not reachable
            else {
                $serverPing->setPingStatus(false);
            }

            //TODO Write a history!

            $em->persist($serverPing);
        }

        $em->flush();

        $output->writeln(count($allServerPing).' ServerPings done!');
    }


    /**
     * @param null $url
     * @return int
     */
    function getHttpCode($url=NULL)
    {
        if($url == NULL) return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }

}
