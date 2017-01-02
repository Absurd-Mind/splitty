<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class OperationController extends Controller {
    /**
     * @Route("/operation/add", name="addOperation")
     */
    public function addOperationAction(Request $request) {
        $operation = new Operation();
        $operation->setDescription('dummy description');
        $operation->setAmount(100);
        $operation->setDatetime(new \DateTime('now'));
        
        $form = $this->createFormBuilder($operation)
        ->add('description', TextType::class)
        ->add('datetime', DateType::class)
        ->add('amount', IntegerType::class)
        ->add('add', SubmitType::class, array('label' => 'Create Operation'))
        ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('info');
        }
        
        return $this->render('operation/add.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
