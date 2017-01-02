<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * Operation
 *
 * @ORM\Table(name="operation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationRepository")
 */
class Operation {
    /**
     *
     * @var int @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @var string @ORM\Column(name="description", type="text")
     */
    private $description;
    
    /**
     *
     * @var \DateTime @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;
    
    /**
     *
     * @var int @ORM\Column(name="amount", type="integer")
     */
    private $amount;
    
    /**
     *
     * @ManyToOne(targetEntity="User")
     */
    private $user1;
    
    /**
     *
     * @ManyToOne(targetEntity="User")
     */
    private $user2;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get amount
     *
     * @return number
     */
    public function getAmount() {
        return $this->amount;
    }
    
    /**
     * Set amount
     * 
     * @param int $amount
     *             
     * @return \AppBundle\Entity\Operation
     */
    public function setAmount($amount) {
        $this->amount = $amount;
        
        return $this;
    }
    
    /**
     * Set description
     *
     * @param string $description            
     *
     * @return Operation
     */
    public function setDescription($description) {
        $this->description = $description;
        
        return $this;
    }
    
    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    /**
     * Set datetime
     *
     * @param \DateTime $datetime            
     *
     * @return Operation
     */
    public function setDatetime($datetime) {
        $this->datetime = $datetime;
        
        return $this;
    }
    
    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime() {
        return $this->datetime;
    }
    
    /**
     * Set user1
     *
     * @param guid $user1            
     *
     * @return Operation
     */
    public function setUser1($user1) {
        $this->user1 = $user1;
        
        return $this;
    }
    
    /**
     * Get user1
     *
     * @return guid
     */
    public function getUser1() {
        return $this->user1;
    }
    
    /**
     * Set user2
     *
     * @param \User $user2            
     *
     * @return Operation
     */
    public function setUser2($user2) {
        $this->user2 = $user2;
        
        return $this;
    }
    
    /**
     * Get user2
     *
     * @return \User
     */
    public function getUser2() {
        return $this->user2;
    }
}

