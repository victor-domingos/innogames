<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/index", name="homepage")
     */
    public function indexAction()
    {
        
        return $this->render('index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
/*
        return $this->render('base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
  */      
/*
        return new Response(
            '<html><body>Hi</body></html>'
        );*/
    }
}
