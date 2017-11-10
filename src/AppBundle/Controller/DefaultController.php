<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/live", name="live_monitoring")
     * @Route("/")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (date('N')>5 || date('Hi') >= 1815 || date('Hi') <= '845')
        {
        return $this->render('default/black.html.twig');
        }

        $repo = $this->getDoctrine()->getRepository('AppBundle:Status');
        $entities = $repo->findAll();

        return $this->render('default/index.html.twig',
            [
                'entities' => $entities,
            ]);
    }
}