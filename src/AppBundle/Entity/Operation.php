<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embedded;
use Money\Money;

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
     * @Embedded(class = "Money\Money")
     */
    private $amount;
    
    /**
     * @ORM\OneToMany(targetEntity="Proceeding", mappedBy="operation")
     */
    private $proceedings;
    
    private $users;
    
    /**
     * @ORM\OneToMany(targetEntity="Split", mappedBy="operation")
     */
    private $splits;

    public function __construct() {
        $this->proceedings = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->splits = new ArrayCollection();
    }
    
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
     * @return Money
     */
    public function getAmount() {
        return $this->amount;
    }
    
    /**
     * Set amount
     *
     * @param Money $amount
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
    
    public function getProceedings() {
        return $this->proceedings;
    }
    
    public function getUsers() {
        return $this->users;
    }
    
    public function getSplits() {
        return $this->splits;
    }
    
    public function setSplits($splits) {
        
        $this->splits = $splits;
        
        return $this;
    }
    
    public function isPayement() {
        return $this->splits->isEmpty();
    }
}

