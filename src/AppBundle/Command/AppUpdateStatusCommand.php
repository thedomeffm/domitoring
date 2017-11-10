<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppUpdateStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:update:status')
            ->setDescription('check the status of all entries (status.php)')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $repo = $doctrine->getRepository('AppBundle:Status');

        $entries = $repo->findAll();

        if ($entries === null){
            $output->writeln('<error>No entries to check!</error>');
            return -1;
        }

        foreach ($entries as $entry){
            //check the url | if true set success to new datetime
            if ($this->urlExists($entry->getUrl())){
                new \DateTime();
                $entry->setLastSuccess(new \DateTime());
                $entry->setLastError(null);
            }
            //if no response from server set error to the date (if there is already a datetime i dont want to delete this!
            //the datetime in error will be deleted if we get an success response!
            else
            {
                if ($entry->getLastError() === null){
                    $entry->setLastError(new \DateTime());
                }
            }
            $em->persist($entry);
            $em->flush();
        }
    }

    /**
     * @param null $url
     * @return bool
     */
    function urlExists($url=NULL)
    {
        if($url == NULL) return false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode>=200 && $httpcode<300){
            return true;
        } else {
            return false;
        }
    }

}
