<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
*@ORM\Entity
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
}

?>