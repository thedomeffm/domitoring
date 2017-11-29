<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ServerBlock;
use AppBundle\Entity\ServerPing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MonitoringController extends Controller
{
    /**
     * @Route("/monitoring", name="monitoring")
     * @Route("/index")
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $pings = $doctrine->getRepository('AppBundle:ServerPing')->findAll();

        /** @var ServerPing $ping */
        foreach ($pings as $ping)
        {
            $ping->httpCodeText = $this->getHttpInfo($ping->getPingHttpCode());
        }

        $blocks = $doctrine->getRepository('AppBundle:ServerBlock')->findAll();

        $form = $this->createFormBuilder()
            ->add('user', TextType::class,[
                'label' => 'User',
                'attr'  => ['maxlength' => 10]
            ])
            ->add('userMail', TextType::class,[
                'label' => 'User Mail',
                'attr'  => ['maxlength' => 32]
            ])
            ->add('reason', TextType::class,[
                'label' => 'Reason',
                'attr'  => ['maxlength' => 16]
            ])
            ->add('id', HiddenType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Block-it'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form = $request->request->get('form');

            $id = $form['id'];
            $user = $form['user'];
            $reason = $form['reason'];

            $em = $this->getDoctrine()->getManager();
            $server = $em->getRepository('AppBundle:ServerBlock')->find($id);

            //error if server is already blocked
            if ($server->getFree() === false){
                $this->addFlash(
                    'error',
                    'Server already blocked'
                );

                return $this->redirectToRoute('monitoring');
            }

            $server->setFree(false);
            $server->setUser($user);
            $server->setReason($reason);
            $server->setBlockedSince(new \DateTime());

            $em->persist($server);
            $em->flush();
            return $this->redirectToRoute('monitoring');
        }

        $lastAccident = $doctrine->getRepository('AppBundle:HistoryServerPing')->getLastAccident();
        $lastAccident = $lastAccident[0];

        $today = new \DateTime();
        $diff = $today->diff($lastAccident->getPingDatetime());

        return $this->render('Monitoring/index.html.twig',
            [
                'lastAccident' => $diff,
                'pings' => $pings,
                'blocks' => $blocks,
                'formObject' => $form,
            ]);
    }

    /**
     * @Route("/history", name="history")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyAction()
    {
        $doctrine = $this->getDoctrine();

        $history = $doctrine->getRepository('AppBundle:HistoryServerPing')->findBy([],['pingDatetime' => 'DESC']);

        $date = new \DateTime('-1day');
        $count1 = count($doctrine->getRepository('AppBundle:HistoryServerPing')->getBetweenDatetimeAndToday($date));
        $date = new \DateTime('-7day');
        $count7 = count($doctrine->getRepository('AppBundle:HistoryServerPing')->getBetweenDatetimeAndToday($date));
        $date = new \DateTime('-30day');
        $count30 = count($doctrine->getRepository('AppBundle:HistoryServerPing')->getBetweenDatetimeAndToday($date));

        return $this->render('Monitoring/history.html.twig',
            [
                'count1' => $count1,
                'count7' => $count7,
                'count30' => $count30,
                'history' => $history,
            ]);
    }

    /**
     * @Route("/status/ping", name="ping_status", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getPingStatus()
    {
        $pings = $this->getDoctrine()->getManager()->getRepository('AppBundle:ServerPing')->findAll();

        $pingStatus = [];

        /**
         * @var ServerPing $ping
         */
        foreach ($pings as $ping) {
            $pingStatus[] = [
                "id"                => $ping->getId(),
                //"name"              => $ping->getName(),
                "pingDatetime"      => $ping->getPingDatetime()->format('H:i'),
                "pingSuccess"        => $ping->getPingSuccess(),
                "pingHttpCode"      => $ping->getPingHttpCode(),
                "pingHttpCodeText"  => $this->getHttpInfo($ping->getPingHttpCode()),
            ];
        }

        return new JsonResponse($pingStatus);
    }

    /**
     * @Route("/status/block", name="block_status", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getBlockStatus()
    {
        $blocks = $this->getDoctrine()->getManager()->getRepository('AppBundle:ServerBlock')->findAll();

        $blockStatus = [];

        /**
         * @var ServerBlock $block
         */
        foreach ($blocks as $block) {
            if ($block->getBlockedSince() != null)
            {
                $blockStatus[] = [
                    "id"            => $block->getId(),
                    "name"          => $block->getName(),
                    "free"          => $block->getFree(),
                    "user"          => $block->getUser(),
                    "reason"        => $block->getReason(),
                    "blockedSince"  => $block->getBlockedSince()->format('H:i'),
                ];
            }
            else{
            $blockStatus[] = [
                "id"            => $block->getId(),
                "name"          => $block->getName(),
                "free"          => $block->getFree(),
                "user"          => $block->getUser(),
                "reason"        => $block->getReason(),
                "blockedSince"  => $block->getBlockedSince(),
            ];
            }
        }

        return new JsonResponse($blockStatus);
    }

    /**
     * @Route("/status/error", name="error_status", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getLastErrorStatus()
    {
        $lastAccident = $this->getDoctrine()->getRepository('AppBundle:HistoryServerPing')->getLastAccident();
        $lastAccident = $lastAccident[0];

        $today = new \DateTime();
        $diff = $today->diff($lastAccident->getPingDatetime());
        $lastErrorStatus = [];
        $lastErrorStatus[] = [
            "y" => $diff->y,
            "m" => $diff->m,
            "d" => $diff->d,
            "h" => $diff->h,
            "i" => $diff->i,
        ];

        return new JsonResponse($lastErrorStatus);
    }

    /**
     * @Route("/status/free/{id}", name="free_block_server")
     * @param ServerBlock $server
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setFree(ServerBlock $server)
    {
        $em = $this->getDoctrine()->getManager();

        $server->setFree(true);
        $server->setReason(null);
        $server->setUser(null);

        $date = new \DateTime('2000-01-01 00:00:00');

        $server->setBlockedSince($date);

        $em->persist($server);
        $em->flush();

        return $this->redirectToRoute('monitoring');
    }

    /**
     * Get the int and return the string code
     *
     * @param int $httpCode
     * @return string
     */
    public function getHttpInfo(int $httpCode)
    {
        $httpInfo = 'Unkown - TimeOut';

        switch ($httpCode)
        {
                //100

            case 100:
                $httpInfo = "Continue";
                break;
            case 101:
                $httpInfo = "Switching Protocols";
                break;
            case 102:
                $httpInfo = "Processing";
                break;

                //200

            case 200:
                $httpInfo = "OK";
                break;
            case 201:
                $httpInfo = "Created";
                break;
            case 202:
                $httpInfo = "Accepted";
                break;
            case 203:
                $httpInfo = "Non-Authoritative Information";
                break;
            case 204:
                $httpInfo = "No Content";
                break;
            case 205:
                $httpInfo = "Reset Content";
                break;
            case 206:
                $httpInfo = "Partial Content";
                break;
            case 207:
                $httpInfo = "Multi-Status";
                break;
            case 208:
                $httpInfo = "Already Reported";
                break;
            case 226:
                $httpInfo = "IM Used";
                break;

                //300

            case 300:
                $httpInfo = "Multiple Choices";
                break;
            case 301:
                $httpInfo = "Moved Permanently";
                break;
            case 302:
                $httpInfo = "Found (Moved Temporarily)";
                break;
            case 303:
                $httpInfo = "See Other";
                break;
            case 304:
                $httpInfo = "Not Modified";
                break;
            case 305:
                $httpInfo = "Use Proxy";
                break;
            case 306:
                $httpInfo = "-none-";
                break;
            case 307:
                $httpInfo = "Temporary Redirect";
                break;
            case 308:
                $httpInfo = "Permanent Redirect";
                break;

                //400

            case 400:
                $httpInfo = "Bad Request";
                break;
            case 401:
                $httpInfo = "Unauthorized";
                break;
            case 402:
                $httpInfo = "Payment Required";
                break;
            case 403:
                $httpInfo = "Forbidden";
                break;
            case 404:
                $httpInfo = "Not Found";
                break;
            case 405:
                $httpInfo = "Method Not Allowed";
                break;
            case 406:
                $httpInfo = "Not Acceptable";
                break;
            case 407:
                $httpInfo = "Proxy Authentication Required";
                break;
            case 408:
                $httpInfo = "Request Time-out";
                break;
            case 409:
                $httpInfo = "Conflict";
                break;
            case 410:
                $httpInfo = "Gone";
                break;
            case 411:
                $httpInfo = "Length Required";
                break;
            case 412:
                $httpInfo = "Precondition Failed";
                break;
            case 413:
                $httpInfo = "Request Entity Too Large";
                break;
            case 414:
                $httpInfo = "URI Too Long";
                break;
            case 415:
                $httpInfo = "Unsupported Media Type";
                break;
            case 416:
                $httpInfo = "Requested range not satisfiable";
                break;
            case 417:
                $httpInfo = "Expectation Failed";
                break;
            case 420:
                $httpInfo = "Policy Not Fulfilled";
                break;
            case 421:
                $httpInfo = "Misdirected Request";
                break;
            case 422:
                $httpInfo = "Unprocessable Entity";
                break;
            case 423:
                $httpInfo = "Locked";
                break;
            case 424:
                $httpInfo = "Failed Dependency";
                break;
            case 426:
                $httpInfo = "Upgrade Required";
                break;
            case 428:
                $httpInfo = "Precondition Required";
                break;
            case 429:
                $httpInfo = "Too Many Requests";
                break;
            case 431:
                $httpInfo = "Request Header Fields Too Large";
                break;
            case 451:
                $httpInfo = "Unavailable For Legal Reasons";
                break;
            case 418:
                $httpInfo = "I am a teapot";
                break;
            case 425:
                $httpInfo = "Unordered Collection";
                break;
            case 444:
                $httpInfo = "No Response";
                break;
            case 449:
                $httpInfo = "The request should be retried after doing the appropriate action";
                break;
            case 499:
                $httpInfo = "Client Closed Request";
                break;

                //500

            case 500:
                $httpInfo = "Internal Server Error";
                break;
            case 501:
                $httpInfo = "Not Implemented";
                break;
            case 502:
                $httpInfo = "Bad Gateway";
                break;
            case 503:
                $httpInfo = "Service Unavailable";
                break;
            case 504:
                $httpInfo = "Gateway Time-out";
                break;
            case 505:
                $httpInfo = "HTTP Version not supported";
                break;
            case 506:
                $httpInfo = "Variant Also Negotiates";
                break;
            case 507:
                $httpInfo = "Insufficient Storage";
                break;
            case 508:
                $httpInfo = "Loop Detected";
                break;
            case 509:
                $httpInfo = "Bandwidth Limit Exceeded";
                break;
            case 510:
                $httpInfo = "Not Extended";
                break;
            case 511:
                $httpInfo = "Network Authentication Required";
                break;
        }

        return $httpInfo;
    }
}
