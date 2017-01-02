<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use AppBundle\Entity\Proceeding;
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
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
            $other = $userRepository->findOneById(2);

            $proceeding = new Proceeding();
            $proceeding->setDate(new \DateTime('now'));
            $proceeding->setAmount($operation->getAmount()/2);
            $proceeding->setOperation($operation);
            $proceeding->setUser1($this->getUser());
            $proceeding->setUser2($other);

            $em = $this->getDoctrine()->getManager();
            $em->persist($proceeding);


            $operation->getProceedings()->add($proceeding);

            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('info');
        }
        
        return $this->render('operation/add.html.twig', array(
                'form' => $form->createView(),
        ));
    }
}
