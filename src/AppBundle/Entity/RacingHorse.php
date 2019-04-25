<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *@ORM\Entity(repositoryClass="AppBundle\Repository\RacingHorseRepository")
 *@ORM\Table(name="racing_horse")
 */

class RacingHorse {

    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Race", inversedBy="racingHorseRaces")
     */
    private $race;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Horse", inversedBy="racingHorseHorses")
     */
    private $horse;

    /**
     *
     * @ORM\Column(name="distance_covered", type="decimal", precision=7, scale=3)
     */
    private $distanceCovered;

    /**
     *
     * @ORM\Column(name="time_in_seconds", type="decimal", precision=10, scale=3)
     */
    private $timeInSeconds;


    public function __construct()
    {
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRace(){
        return $this->race;
    }

    public function setRace($race){
        $this->race = $race;
    }

    public function getHorse(){
        return $this->horse;
    }

    public function setHorse($horse){
        $this->horse = $horse;
    }

    public function getDistanceCovered(){
        return $this->distanceCovered;
    }

    public function setDistanceCovered($distanceCovered){
        $this->distanceCovered = $distanceCovered;
    }

    public function getTimeInSeconds(){
        return $this->timeInSeconds;
    }

    public function setTimeInSeconds($timeInSeconds){
        $this->timeInSeconds = $timeInSeconds;
    }


}
?>