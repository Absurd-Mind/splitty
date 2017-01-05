<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FriendsRestController extends FOSRestController
{
    /**
     * adds a friend with the given email address.<br/>
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="email", nullable=false, strict=true, description="e-mail address.")
     *
     * @return View
     */
    public function putFriendAction(ParamFetcher $paramFetcher)
    {
        $view = View::create();
        $email = $paramFetcher->get('email');
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AppBundle:User');
        $otherUser = $userRepository->findOneByEmail($email);
        if ($otherUser == null || $this->getUser()->getFriends()->contains($otherUser)) {
            $view = $this->getErrorsView(array('no user with email address found'));
            return $this->handleView($view);
        }
        $this->getUser()->getFriends()->add($otherUser);
        $em->flush();
        
        $view->setStatusCode(200);
        return $this->handleView($view);
    }
    
    /**
     * removes a friend with the given e-mail.<br/>
     *
     * @param ParamFetcher $paramFetcher Paramfetcher
     *
     * @RequestParam(name="email", nullable=false, strict=true, description="e-mail address of the friend to be removed.")
     *
     * @return View
     */
    public function deleteFriendAction(ParamFetcher $paramFetcher)
    {
        $view = View::create();
        $email = $paramFetcher->get('email');
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AppBundle:User');
        $otherUser = $userRepository->findOneByEmail($email);
        if ($otherUser == null || !$this->getUser()->getFriends()->contains($otherUser)) {
            $view = $this->getErrorsView(array('no user with email address found'));
            return $this->handleView($view);
        }
        $this->getUser()->getFriends()->removeElement($otherUser);
        $em->flush();
        
        $view->setStatusCode(200);
        return $this->handleView($view);
    }
}
