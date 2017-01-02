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
        /*
         * $query = $repository->createQueryBuilder('o')
         * ->where('o.user1 = :userId OR o.user2 = :userId')
         * ->setParameter('userId', $this->getUser()->getId())
         * ->getQuery();
         */
        // $query->getResult();
        $operations = $repository->findAll();
        
        // replace this example code with whatever you need
        return $this->render('user/index.html.twig', [ 
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'operations' => $operations 
        ]);
    }

    /**
     * @Route("/user/show/", name="showuserdebt")
     */
    public function showAction(Request $request) {
        $userId = 2;
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $otherUser = $userRepository->findOneById($userId);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT p
                FROM AppBundle:Proceeding p
                WHERE (p.user1 = :userId OR p.user2 = :userId) AND(p.user1 = :myId OR p.user2 = :myId)
                ORDER BY p.date ASC')->setParameter('myId', $this->getUser()->getId())->setParameter('userId', $userId);
        $proceedings = $query->getResult();
        $sum = 0;
        foreach ( $proceedings as $proceeding ) {
            if ($proceeding->getUser1()->getId() == $this->getUser()->getId()) {
                $sum += $proceeding->getAmount();
            } else {
                $sum -= $proceeding->getAmount();
            }
        }
        return $this->render('user/show.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'proceedings' => $proceedings,
                'sum' => $sum,
                'otherUser' => $otherUser
        ]);
    }
}
