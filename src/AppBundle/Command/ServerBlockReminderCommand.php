<?php

namespace AppBundle\Command;

use AppBundle\Entity\ServerBlock;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServerBlockReminderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ServerBlockReminder')
            ->setDescription('after 1h of block a server, you get an reminder')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $serverBlockRepo = $doctrine->getRepository('AppBundle:ServerBlock');
        $allServerBlock = $serverBlockRepo->findAll();

        if ($allServerBlock == null)
        {
            $output->writeln('<error>No entries to remind!</error>');
            return false;
        }

        $now = new \DateTime();
        $now = intval($now->format('Hi'));

        /** @var ServerBlock $server */
        foreach ($allServerBlock as $server)
        {
            $serverDate = intval($server->getBlockedSince()->format('Hi'));
            $diff = $now - $serverDate;
            if ($server->getFree() === false && $diff > 90 && $diff < 300)
            {
                if ($server->getUserMail())
                {
                    $this->sendReminder($server->getUserMail(), $server->getName(), $this->getMailer());
                }
            }
        }

        $output->writeln('Reminder command done.');
    }

    /**
     * Send the register confirmation mail
     *
     * @param $mail
     * @param $server
     * @param \Swift_Mailer $mailer
     */
    public function sendReminder($mail, $server, \Swift_Mailer $mailer)
    {
        $date = new \DateTime();
        //create the subject here
        $subject = 'Block: ' . $server . ' | Domitoring ' . $date->format('d.M | H:i');
        //Create Message
        $message = new \Swift_Message();
        //combine everything
        $message
            ->setSubject($subject)
            ->setFrom('reservix.blackboard@gmail.com')
            ->setTo($mail)
            ->setReplyTo('domenic.gerhold@reservix.de')
            ->setBody(
                $this->getContainer()->get('templating')->render(
                    'Mail/reminder.html.twig',
                    [
                        'server' => $server,
                    ]
                ),
                'text/html'
            );
        /*
         * PHP Exception Swift_TransportException: "Connection could not be established with host smtp.gmail.com [Network is unreachable #101]" at .../blackboard/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/StreamBuffer.php line 268
         */
        try {
            $mailer->send($message);
        } catch (\Swift_TransportException $e) {
            sleep(3);
            $mailer->send($message);
        }
        return;
    }
    /**
     * @return object|\Swift_Mailer
     */
    private function getMailer()
    {
        return $this->getContainer()->get('mailer');
    }
}

