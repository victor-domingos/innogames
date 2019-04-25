<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity(repositoryClass="AppBundle\Repository\HorseRepository")
 *@ORM\Table(name="horse")
 */

class Horse {

    const BASE_SPEED = 5;
    const ENDURANCE_MULTIPLIER = 100;
    const JOCKEY_SLOWDOWN = 5;
    const STRENGTH_MULTIPLIER = 8;

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
    *
    * @ORM\Column(type="boolean")
    */
    private $running;

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

    public function getRunning(){
        return $this->running;
    }

    public function setRunning($running){
        $this->running = $running;
    }

    public function getRacingHorseHorses(){
        return $this->racingHorseHorses;
    }

    //Those below are used in the race
    public function getNormalSpeed(){
        return $this->getSpeed() + constant('self::BASE_SPEED');
    }

    //Checkpoint is when the horse becomes slower
    public function getCheckpoint(){
        return $this->getEndurance() * constant('self::ENDURANCE_MULTIPLIER');
    }

    public function getSlowerSpeed(){
        return $this->getNormalSpeed() - constant('self::JOCKEY_SLOWDOWN') 
            * (100 - ($this->getStrength() * constant('self::STRENGTH_MULTIPLIER'))) / 100;
    }

    //Checks which speed is used to start based on the starting point, whether it is before or after the checkpoint
    public function getStartingSpeed($startingPoint){
        return $startingPoint < $this->getCheckpoint() ? $this->getNormalSpeed() : $this->getSlowerSpeed();
    }

    //Calculates the time left after reaching the checkpoint by using the time in which the horse reached the checkpoint
    //with normal speed. Then, applies the slower speed at the time left
    public function calculateFinalDistanceAfterCheckpoint($fullTime, $startingPoint, $finalDistance){
        $timeToCheckpoint = ($this->getCheckpoint() - $startingPoint) * $fullTime / ($finalDistance - $startingPoint);
        $timeAfterCheckpoint = $fullTime - $timeToCheckpoint;

        //To know where the horse will finish the progress, it will use the left time with slower speed and add to the checkpoint
        return $timeToCheckpoint * $this->getSlowerSpeed() + $this->getCheckpoint();
    }
}

?>