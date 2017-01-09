<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\Collection;
use Money\Money;
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
                'operations' => $operations,
                'user' => $this->getUser()
        ]);
    }

    private function sumProceedings(Collection $proceedings, int $myId, int $containedUserId) {
        $sum = Money::EUR(0);
        foreach ( $proceedings as $proceeding ) {
            $user1Id = $proceeding->getUser1()->getId();
            $user2Id = $proceeding->getUser2()->getId();

            if ($user1Id != $containedUserId && $user2Id != $containedUserId) {
                continue;
            }

            if ($user1Id == $user2Id) {
                continue;
            }

            if ($sum == null) {
                $sum = new Money(0, $proceeding->getAmount()->getCurrency());
            }

            if ($user1Id == $myId) {
                $sum = $sum->add($proceeding->getAmount());
            } else {
                $sum = $sum->subtract($proceeding->getAmount());
            }
        }
        return $sum;
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
                JOIN o.splits s1
                WITH s1.user = :myId
                JOIN o.splits s2
                WITH s2.user = :otherId
                ORDER BY o.datetime DESC')->setParameter('myId', $this->getUser()->getId())->setParameter('otherId', $otherUser->getId());
        $operations = $query->getResult();
        $sum = Money::EUR(0);
        $summarySums = array();
        $myId = $this->getUser()->getId();
        foreach ($operations as $operation) {
            $sum = $sum->add($this->sumProceedings($operation->getProceedings(), $myId, $userId));
            $summarySums[$operation->getId()] = $this->sumProceedings($operation->getProceedings(), $myId, $myId);
        }
        
        return $this->render('user/show.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'sum' => $sum,
                'summarySums' => $summarySums,
                'user' => $this->getUser(),
                'otherUser' => $otherUser,
                'operations' => $operations
        ]);
    }
}
