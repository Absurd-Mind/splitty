<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use AppBundle\Entity\Proceeding;
use AppBundle\Entity\Split;
use AppBundle\Entity\SplitType;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\IntlMoneyParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class OperationController extends Controller {
    
    /**
     * @Route("/payment/add/{userId}", name="addPayment")
     */
    public function addPaymentAction(Request $request, int $userId) {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AppBundle:User');
        $other = $userRepository->findOneById($userId);
        $me = $userRepository->findOneById($this->getUser()->getId());
        $data = array('date' => new \DateTime('now'),
                'currency' => 'EUR',
                'amount' => '0.00'
        );
        $friends = $me->getFriends()->toArray();
        $friends[] = $me;
        
        $currencies = array();
        $isoCurrencies = new ISOCurrencies();
        foreach ($isoCurrencies as $currency) {
            $currencies[] = $currency->getCode();
        }
        
        $builder = $this->createFormBuilder($data)
        ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                        'class' => 'js-datepicker',
                )
        ))
        ->add('currency', ChoiceType::class,[
                'choices' => $currencies,
                'choice_label' => function($value, $key, $index) use (&$currencies) {
                    return $value;
                },
                'group_by' => function($value, $key, $index) {
                    return substr($value, 0, 1);  
                },
                'attr' => array(
                    'class' => 'currencyinput'
                )
        ])
        ->add('amount', TextType::class, array('attr' => array('id' => 'moneyinput')))
        ->add('sender', ChoiceType::class, array (
                'choices' => $friends,
                'expanded' => false,
                'choice_label' => function($value, $key, $index) {
                    return $value->getUsername();
                },
                'multiple' => false,
                'attr' => array (
                        'class' => 'js-example-basic-multiple'
                ),
                'data' => $me
        ))
        ->add('receiver', ChoiceType::class, array (
                'choices' => $friends,
                'choice_label' => function($value, $key, $index) {
                return $value->getUsername();
                },
                'multiple' => false,
                'attr' => array (
                        'class' => 'js-example-basic-multiple'
                ),
                'data' => $other
        ))
        ->add('add', SubmitType::class, array (
                'label' => 'Create Operation'
        ));
        
        $form = $builder->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            

            $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            $moneyParser = new IntlMoneyParser($numberFormatter, $isoCurrencies);
            
            $money = $moneyParser->parse($data['currency'].$data['amount']);
            
            
            $operation = new Operation();
            $operation->setAmount($money);
            $operation->setDatetime($data['date']);
            $operation->setDescription('');
            $operation->setType(\SplitType::Payment);
            
            $proceeding = new Proceeding();
            $proceeding->setOperation($operation);
            $proceeding->setDate($operation->getDatetime());
            $proceeding->setUser1($data['sender']);
            $proceeding->setUser2($data['receiver']);
            $proceeding->setAmount($money);
            
            $split1 = new Split();
            $split1->setOperation($operation);
            $split1->setUser($data['sender']);
            $split1->setPaid($money);
            $split1->setDebt(new Money(0, $money->getCurrency()));
            $operation->getSplits()->add($split1);
            
            $split2 = new Split();
            $split2->setOperation($operation);
            $split2->setUser($data['receiver']);
            $split2->setPaid(new Money(0, $money->getCurrency()));
            $split2->setDebt($money);
            $operation->getSplits()->add($split2);
            
            $operation->getProceedings()->add($proceeding);
            
            $em->persist($split1);
            $em->persist($split2);
            $em->persist($proceeding);
            $em->persist($operation);
            if ($me->getFriends()->contains($me)) {
                $me->getFriends()->removeElement($me);
            }
            
            $em->flush();
            
            return $this->redirectToRoute('showuserdebt', array('userId' => $userId));
        }
        
        return $this->render('payment/add.html.twig', array (
                'form' => $form->createView(),
                'user' => $this->getUser()
        ));
    }
    
    /**
     * @Route("/operation/add/{userId}", name="addOperation")
     */
    public function addOperationAction(Request $request, int $userId) {
        $me = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AppBundle:User');
        $other = $userRepository->findOneById($userId);
        
        $data = array(
                'description' => '',
                'amount' => '0.00',
                'datetime' => new \DateTime('now'),
                'currency' => 'EUR',
                'users' => array($other),
                'sender' => $me,
                'type' => SplitType::Even
        );

        
        $friends = $me->getFriends()->toArray();
        $friendsAndMe = $me->getFriends()->toArray();
        $friendsAndMe[] = $me;
        
        $currencies = array();
        $isoCurrencies = new ISOCurrencies();
        foreach ($isoCurrencies as $currency) {
            $currencies[] = $currency->getCode();
        }
        
        $builder = $this->createFormBuilder($data)
        ->add('description', TextType::class)
        ->add('datetime', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => array(
                        'class' => 'js-datepicker',
                )
        ))
        ->add('currency', ChoiceType::class,[
                'choices' => $currencies,
                'choice_label' => function($value, $key, $index) {
                    return $value;
                },
                'group_by' => function($value, $key, $index) {
                    return substr($value, 0, 1);
                },
                'attr' => array(
                    'class' => 'currencyinput'
                )
                ])
        ->add('amount', TextType::class, array('attr' => array('id' => 'moneyinput')
                
        ))->add('type', ChoiceType::class, array (
                'choices' => array(SplitType::Even, SplitType::YouOwe, SplitType::TheyOwe),
                'expanded' => false,
                'choice_label' => function ($value, $key, $index) {
                    switch ($value) {
                        case SplitType::YouOwe :
                            return "you owe everything";
                        case SplitType::TheyOwe :
                            return "they owe everything";
                        case SplitType::Even :
                        default :
                            return "spend even";
                    }
                },
                'multiple' => false,
                'attr' => array (
                    'class' => 'js-example-basic-multiple' 
                )
        ))->add('sender', ChoiceType::class, array (
                'choices' => $friendsAndMe,
                'expanded' => false,
                'choice_label' => function ($value, $key, $index) {
                    return $value->getUsername();
                },
                'multiple' => false,
                'attr' => array (
                    'class' => 'js-example-basic-multiple' 
                )
        ))->add('users', ChoiceType::class, array (
                'choices' => $friends,
                'choice_label' => function ($value, $key, $index) {
                    return $value->getUsername();
                },
                'multiple' => true,
                'attr' => array (
                    'class' => 'js-example-basic-multiple' 
                )
        ))->add('add', SubmitType::class, array (
                'label' => 'Create Operation' 
        ));

        
        $form = $builder->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $operationUsers = $data['users'];
            $operationUsers[] = $this->getUser();
            $type = $data['type'];
            $i = 0;
            
            $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            $moneyParser = new IntlMoneyParser($numberFormatter, $isoCurrencies);
            
            $money = $moneyParser->parse($data['currency'].$data['amount']);
            $count = count($operationUsers);
            if ($type == SplitType::TheyOwe) {
                $count = $count - 1;
            }
            $mm = $money->allocateTo($count);
            $operation = new Operation();
            $operation->setDescription($data['description']);
            $operation->setDatetime($data['datetime']);
            $operation->setAmount($money);
            $operation->setType($type);
            
            $payer = $data['sender'];
            foreach ( $operationUsers as $other ) {
                $split = new Split();
                $split->setOperation($operation);
                $split->setUser($other);
                if ($other->getId() == $payer->getId()) {
                    $split->setPaid($money);
                    if ($type == SplitType::TheyOwe) {
                        $split->setDebt(new Money(0, $money->getCurrency()));
                    } else if ($type == SplitType::YouOwe) {
                        $split->setDebt($money);
                    }
                } else {
                    $split->setPaid(new Money(0, $money->getCurrency()));
                    if ($type == SplitType::TheyOwe) {
                        $split->setDebt($mm[$i]);
                        $i++;
                    } else if ($type == SplitType::YouOwe) {
                        $split->setDebt(new Money(0, $money->getCurrency()));
                    }
                }
                if ($type == SplitType::Even) {
                    $split->setDebt($mm[$i]);
                    $i++;
                }
                
                $operation->getSplits()->add($split);
                $em->persist($split);
            }
            
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
            return $this->redirectToRoute('showuserdebt', array('userId' => $userId));
        }
        
        return $this->render('operation/add.html.twig', array (
                'form' => $form->createView(),
                'user' => $this->getUser()
        ));
    }
}
