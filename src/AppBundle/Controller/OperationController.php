<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use AppBundle\Entity\Split;
use Money\Currency;
use Money\Money;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Proceeding;

class OperationController extends Controller {
    /**
     * @Route("/operation/add/{userId}", name="addOperation")
     */
    public function addOperationAction(Request $request, int $userId) {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $allUsers = $userRepository->findAll();
        $operation = new Operation();
        $operation->setDescription('dummy description');
        $operation->setAmount(Money::EUR(100));
        $operation->setDatetime(new \DateTime('now'));
        

        $builder = $this->createFormBuilder($operation)
        ->add('description', TextType::class)
        ->add('datetime', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                        'class' => 'js-datepicker',
                )
        ))
        ->add('amount', TextType::class)
        ->add('users', EntityType::class, array (
                // query choices from this entity
                'class' => 'AppBundle:User',
                
                // use the User.username property as the visible option string
                'choice_label' => 'username',
                
                // used to render a select box, check boxes or radios
                'multiple' => true,
                'attr' => array (
                        'width' => '400',
                        'class' => 'js-example-basic-multiple' 
                )
        ))->add('add', SubmitType::class, array (
                'label' => 'Create Operation' 
        ));
        
        
        $builder->get('amount')
        ->addModelTransformer(new CallbackTransformer(
                function ($money) {
                    // transform the array to a string
                    return $money->getAmount();
                },
                function ($string) {
                    // transform the string back to an array
                    return new Money($string, new Currency('EUR'));
                }
                ))
                ;
        
        $form = $builder->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $operationUsers = $operation->getUsers();
            $operationUsers->add($this->getUser());
            $i = 0;
            $mm = $operation->getAmount()->allocateTo($operation->getUsers()->count());
            foreach ( $operationUsers as $other ) {
                $split = new Split();
                $split->setOperation($operation);
                $split->setUser($other);
                if ($other->getId() == $this->getUser()->getId()) {
                    $split->setPaid($operation->getAmount());
                } else {
                    $split->setPaid(new Money(0, $operation->getAmount()->getCurrency()));
                }
                $split->setDebt($mm[$i]);
                $i++;
                
                $operation->getSplits()->add($split);
                $em->persist($split);
            }
            
            $payer = $this->getUser();
            foreach ($operation->getSplits() as $split) {
                if ($split->getUser()->getId() == $payer->getId()) {
                    continue;
                }
                
                $proceeding = new Proceeding(); 
                $proceeding->setOperation($operation);
                $proceeding->setDate($operation->getDatetime());
                $proceeding->setUser1($payer);
                $proceeding->setUser2($split->getUser());
                $proceeding->setAmount($split->getDebt());
                
                $operation->getProceedings()->add($proceeding);
                $em->persist($proceeding);
            }
            $em->persist($operation);
            $em->flush();
            return $this->redirectToRoute('info');
        }
        
        return $this->render('operation/add.html.twig', array (
                'form' => $form->createView(),
                'users' => $allUsers 
        ));
    }
}
