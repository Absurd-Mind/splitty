<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\Collection;
use Money\Money;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {
    /**
     * @Route("/", name="dashboard")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $myId = $this->getUser()->getId();
        $query = $em->createQuery('SELECT p
                FROM AppBundle:Proceeding p
                WHERE p.user1 = :myId OR p.user2 = :myId
                ORDER BY p.date DESC')->setParameter('myId', $myId);
        $proceedings = $query->getResult();
        
        $sums = array();
        foreach ($proceedings as $proceeding) {
            $user1Id = $proceeding->getUser1()->getId();
            $user2Id = $proceeding->getUser2()->getId();
            
            if ($user1Id == $user2Id) {
                continue;
            }
            
            $amount = $proceeding->getAmount();
            $code = $amount->getCurrency()->getCode();
            $otherUserId = $user1Id;
            $type = 'subtract';
            
            if ($user1Id == $myId) {
                $otherUserId = $user2Id;
                $type = 'add';
            }
            
            if (!array_key_exists($otherUserId, $sums)) {
                $sums[$otherUserId] = array();
            }
            
            if (!array_key_exists($code, $sums[$otherUserId])) {
                $sums[$otherUserId][$code] = new Money(0, $amount->getCurrency());
            }
            
            if ($type == 'add') {
                $sums[$otherUserId][$code] = $sums[$otherUserId][$code]->add($amount);
            } else {
                $sums[$otherUserId][$code] = $sums[$otherUserId][$code]->subtract($amount);
            }
        }
        
        $userRepository = $em->getRepository('AppBundle:User');
        $users = array();
        foreach($sums as $userId => $value) {
            $users[$userId] = $userRepository->findOneById($userId);
        }
        
        // replace this example code with whatever you need
        return $this->render('user/index.html.twig', [ 
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'operations' => array(),
                'sums' => $sums,
                'user' => $this->getUser(),
                'users' => $users
        ]);
    }

    private function sumProceedings(Collection $proceedings, int $myId, int $containedUserId) {
        $sum = null;
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
        $sum = array();
        $summarySums = array();
        $myId = $this->getUser()->getId();
        foreach ($operations as $operation) {
            $sum = $this->merge($sum, $this->sumProceedings($operation->getProceedings(), $myId, $userId));
            $summarySums[$operation->getId()] = $this->sumProceedings($operation->getProceedings(), $myId, $myId);
        }
        
        return $this->render('user/show.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'sums' => $sum,
                'summarySums' => $summarySums,
                'user' => $this->getUser(),
                'otherUser' => $otherUser,
                'operations' => $operations
        ]);
    }
    
    private function merge(array $sum, Money $n) {
        if ($n == null) {
            return;
        }
        
        $code = $n->getCurrency()->getCode();
        if (array_key_exists($code, $sum)) {
            $sum[$code] = $sum[$code]->add($n); 
        } else {
            $sum[$code] = $n;
        }
        
        return $sum;
    }
}
