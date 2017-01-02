<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\OneToMany(targetEntity="Proceeding", mappedBy="operation")
     */
    private $proceedings;

    public function __construct() {
        $this->proceedings = new ArrayCollection();
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
    
    public function getProceedings() {
        return $this->proceedings;
    }
}

