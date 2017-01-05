<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Money\Money;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Split
 *
 * @ORM\Table(name="split")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SplitRepository")
 */
class Split
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Operation
     *
     * @ManyToOne(targetEntity="Operation", inversedBy="splits")
     */
    private $operation;

    /**
     * @var User
     *
     * @ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @var Money
     *
     * @Embedded(class = "Money\Money")
     */
    private $paid;

    /**
     * @var Money
     *
     * @Embedded(class = "Money\Money")
     */
    private $debt;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set operation
     *
     * @param Operation $operation
     *
     * @return Split
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    
        return $this;
    }
    
    /**
     * Get operation
     *
     * @return Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }
    
    /**
     * Set user
     *
     * @param User $user
     *
     * @return Split
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set paid
     *
     * @param Money $paid
     *
     * @return Split
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return Money
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set debt
     *
     * @param Money $debt
     *
     * @return Split
     */
    public function setDebt($debt)
    {
        $this->debt = $debt;

        return $this;
    }

    /**
     * Get debt
     *
     * @return Money
     */
    public function getDebt()
    {
        return $this->debt;
    }
}

