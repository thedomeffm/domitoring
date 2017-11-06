<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (date('N')>5 || date('Hi') >= 1815 || date('Hi') <= '845')
        {
        return $this->render('default/black.html.twig');
        }

        $elvisLive['name'] = 'Elvis - Live';
        $elvisLive['url'] = 'https://elvis.adticket.de/login';
        $elvisLive['online'] = $this->urlExists($elvisLive['url']);

        $elvisTest['name'] = 'Elvis - Test';
        $elvisTest['url'] = 'https://test-elvis.adticket.de/login';
        $elvisTest['online'] = $this->urlExists($elvisTest['url']);

        $elvisStage['name'] = 'Elvis - Stage';
        $elvisStage['url'] = 'https://stage-elvis.adticket.de/login';
        $elvisStage['online'] = $this->urlExists($elvisStage['url']);

        return $this->render('default/index.html.twig',
            [
                'elvis_live' => $elvisLive,
                'elvis_test' => $elvisTest,
                'elvis_stage' => $elvisStage,
            ]);
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
