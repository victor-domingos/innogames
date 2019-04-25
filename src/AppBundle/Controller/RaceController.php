<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Race;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RaceController extends Controller
{
    /**
     * @Route("/create-race", name="create_race")
     */
    public function createRaceAction()
    {
    	
    	$msg = "Hello World";
        return $this->render('index.html.twig', ['warning' => $msg]);
    }
}
