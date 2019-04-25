<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
*@ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
*@ORM\Table(name="race")
*/

class Race {
	/**
	*
	* @ORM\Id
   	* @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(name="time_elapsed", type="integer")
     */
    private $timeElapsed;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finished;

    public function __construct()
    {
    }

    public function getId() {
    	return $this->id;
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function getTimeElapsed(){
        return $this->timeElapsed;
    }

    public function setTimeElapsed($timeElapsed){
        $this->timeElapsed = $timeElapsed;
    }

    public function getFinished(){
        return $this->finished;
    }

    public function setFinished($finished){
        $this->finished = $finished;
    }

}
?>