<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TestStage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/live", name="live_monitoring")
     * @Route("/")
     * @Route("/live/{id}", name="jabba")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, int $id = null)
    {
        if (date('N')>5 || date('Hi') >= 1815 || date('Hi') <= '745')
        {
        return $this->render('default/black.html.twig');
        }

        $form = $this->createFormBuilder()
            ->add('user', TextType::class,[
                'label' => 'Wer blockt den Server?',
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Begründung?',
            ])
            ->add('id', HiddenType::class)
            ->add('save', SubmitType::class, array('label' => 'Server blockieren'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $form = $request->request->get('form');
            $id = $form['id'];
            $user = $form['user'];
            $description = $form['description'];
            $em = $this->getDoctrine()->getManager();
            $server = $em->getRepository('AppBundle:TestStage')->find($id);

            $server->setFree(false);
            $server->setUser($user);
            $server->setDescription($description);

            $em->persist($server);
            $em->flush();
            return $this->redirectToRoute('live_monitoring');
        }

        $doctrine = $this->getDoctrine();

        $statusRepo = $doctrine->getRepository('AppBundle:Status');
        $entities = $statusRepo->findAll();

        $test_stageRepo = $doctrine->getRepository('AppBundle:TestStage');
        $test_stage = $test_stageRepo->findAll();

        return $this->render('default/index.html.twig',
            [
                'formObject' => $form,
                'entities' => $entities,
                'test_stage' => $test_stage,
            ]);
    }

    /**
     * @Route("/block/{id}", name="block_server")
     * @param Request $request
     * @param TestStage $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function blockServerAction(Request $request, TestStage $id)
    {
        $id->setFree(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($id);
        $em->flush();

        return $this->redirectToRoute('live_monitoring');
    }

    /**
     * @Route("/free/check/{id}", name="free_server")
     * @param TestStage $server
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setFreeAction(TestStage $server)
    {
        $em = $this->getDoctrine()->getManager();

        $server->setFree(true);
        $server->setDescription('');
        $server->setUser('');

        $em->persist($server);
        $em->flush();

        return $this->redirectToRoute('live_monitoring');
    }
}