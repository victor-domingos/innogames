<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/index", name="homepage")
     */
    public function indexAction($createRaceMsg = null) {
        
        return $this->render('index.html.twig', [
            'createRaceMsg' => $createRaceMsg
        ]);
    }
}
?>