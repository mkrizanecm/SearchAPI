<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController {

    public function index()
    {
        /**
         * @Route("/")
         * @Method({"GET", "POST"})
         */
        return $this->render('search/index.html.twig');
    }

    public function call()
    {

    }
}








