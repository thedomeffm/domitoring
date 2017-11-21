<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ServerBlock;
use AppBundle\Entity\ServerPing;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class GeneratorController extends Controller
{
    /**
     * @Route("/generate/index", name="generate_index")
     */
    public function indexAction()
    {
        return $this->render('Generator/index.html.twig', [
            'pings' => $this->getDoctrine()->getRepository('AppBundle:ServerPing')->findAll(),
            'blocks' => $this->getDoctrine()->getRepository('AppBundle:ServerBlock')->findAll(),
        ]);
    }

    /**
     * @Route("/generate/newPing")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newPingAction(Request $request)
    {
        $serverPing = new ServerPing();
        $form = $this->createForm('AppBundle\Form\ServerPingType', $serverPing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //get httpCode
            $httpCode = $this->getHttpCode($serverPing->getUrl());

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

            $em->persist($serverPing);
            $em->flush();

            return $this->redirectToRoute('generate_index');
        }


        return $this->render('Generator/new_ping.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/generate/newBlock")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newBlockAction(Request $request)
    {
        $serverBlock = new ServerBlock();
        $form = $this->createForm('AppBundle\Form\ServerBlockType', $serverBlock);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $serverBlock->setStatus(true);

            $em->persist($serverBlock);
            $em->flush();

            return $this->redirectToRoute('generate_index');
        }

        return $this->render('Generator/new_block.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param null $url
     * @return int
     */
    function getHttpCode($url=NULL)
    {
        if ($url == NULL) return false;
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
