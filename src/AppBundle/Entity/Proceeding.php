<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use Doctrine\ORM\Mapping\Embedded;

/**
 * Proceeding
 *
 * @ORM\Table(name="proceeding")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProceedingRepository")
 */
class Proceeding {
    /**
     *
     * @var int @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @var \DateTime @ORM\Column(name="date", type="datetimetz")
     */
    private $date;
    
    /**
     * @ManyToOne(targetEntity="User")
     */
    private $user1;
    
    /**
     * @ManyToOne(targetEntity="User")
     */
    private $user2;
    
    /**
     *
     * @var Money @Embedded(class = "Money\Money")
     */
    private $amount;
    
    /**
     *
     * @var Operation @ManyToOne(targetEntity="Operation", inversedBy="proceedings")
     *      @JoinColumn(name="operation_id", referencedColumnName="id")
     */
    private $operation;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set date
     *
     * @param \DateTime $date            
     *
     * @return Proceeding
     */
    public function setDate($date) {
        $this->date = $date;
        
        return $this;
    }
    
    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }
    
    /**
     * Set user1
     *
     * @param \stdClass $user1            
     *
     * @return Proceeding
     */
    public function setUser1($user1) {
        $this->user1 = $user1;
        
        return $this;
    }
    
    /**
     * Get user1
     *
     * @return \stdClass
     */
    public function getUser1() {
        return $this->user1;
    }
    
    /**
     * Set user2
     *
     * @param \stdClass $user2            
     *
     * @return Proceeding
     */
    public function setUser2($user2) {
        $this->user2 = $user2;
        
        return $this;
    }
    
    /**
     * Get user2
     *
     * @return \stdClass
     */
    public function getUser2() {
        return $this->user2;
    }
    
    /**
     * Set amount
     *
     * @param Money $amount            
     *
     * @return Proceeding
     */
    public function setAmount($amount) {
        $this->amount = $amount;
        
        return $this;
    }
    
    /**
     * Get amount
     *
     * @return Money
     */
    public function getAmount() {
        return $this->amount;
    }
    
    /**
     * Set operation
     *
     * @param integer $operation            
     *
     * @return Proceeding
     */
    public function setOperation($operation) {
        $this->operation = $operation;
        
        return $this;
    }
    
    /**
     * Get operation
     *
     * @return int
     */
    public function getOperation() {
        return $this->operation;
    }
}

