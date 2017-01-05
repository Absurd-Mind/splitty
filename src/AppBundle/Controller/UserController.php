<?php

namespace AppBundle\Controller;

use Money\Money;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @Route("/user/show/{userId}", name="showuserdebt")
     */
    public function showAction(Request $request, int $userId) {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $otherUser = $userRepository->findOneById($userId);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT o
                FROM AppBundle:Operation o
                JOIN o.splits s
                WITH s.user = :myId
                ORDER BY o.datetime ASC')->setParameter('myId', $this->getUser()->getId());
        $operations = $query->getResult();
        $sum = Money::EUR(0);
        foreach ($operations as $operation) {
            foreach ( $operation->getProceedings() as $proceeding ) {
                if ($sum == null) {
                    $sum = new Money(0, $proceeding->getAmount()->getCurrency());
                }
                if ($proceeding->getUser1()->getId() == $this->getUser()->getId()) {
                    $sum = $sum->add($proceeding->getAmount());
                } else {
                    $sum = $sum->subtract($proceeding->getAmount());
                }
            }
        }
        
        return $this->render('user/show.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'sum' => $sum,
                'otherUser' => $otherUser,
                'operations' => $operations
        ]);
    }
}
