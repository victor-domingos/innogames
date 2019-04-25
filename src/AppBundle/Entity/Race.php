<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
*@ORM\Entity
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

    public function getId() {
    	return $this->id;
    }

    public function setId($id) {
    	$this->id = $id;
    }

}
?>