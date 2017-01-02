<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {
    /**
     * @Route("/user/", name="info")
     */
    public function indexAction(Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Operation');
        $operations = $repository->findAll();
        
        // replace this example code with whatever you need
        return $this->render('user/index.html.twig', [ 
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'operations' => $operations 
        ]);
    }
}
