<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity(repositoryClass="AppBundle\Repository\HorseRepository")
 *@ORM\Table(name="horse")
 */

class Horse {
	/**
	*
	* @ORM\Id
   	* @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
    *
    * @ORM\Column(type="string")
    */
    private $name;

    /**
    *
    * @ORM\Column(type="decimal", precision=3, scale=1)
    */
    private $speed;

    /**
    *
    * @ORM\Column(type="decimal", precision=3, scale=1)
    */
    private $strength;

    /**
    *
    * @ORM\Column(type="decimal", precision=3, scale=1)
    */
    private $endurance;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\RacingHorse", mappedBy="horse")
     */
    private $racingHorseHorses;

    public function __construct()
    {
        $this->racingHorseHorses = new ArrayCollection();
    }

    public function getId() {
    	return $this->id;
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getSpeed(){
        return $this->speed;
    }

    public function setSpeed($speed){
        $this->speed = $speed;
    }

    public function getStrength(){
        return $this->strength;
    }

    public function setStrength($strength){
        $this->strength = $strength;
    }

    public function getEndurance(){
        return $this->endurance;
    }

    public function setEndurance($endurance){
        $this->endurance = $endurance;
    }

    public function getRacingHorseHorses(){
        return $this->racingHorseHorses;
    }

    //Those below are used in the race
    public function getHorseSpeed(){
        return $this->getSpeed() + 5;
    }

    public function getCheckpoint(){
        return $this->getEndurance() * 100;
    }

    public function getSlowerSpeed(){
        return $this->getHorseSpeed() - 5 * (100 - ($this->getEndurance() * 8) / 100);
    }
}

?>