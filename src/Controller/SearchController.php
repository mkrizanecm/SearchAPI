<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\{TextType};


class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search")
     */
    public function index()
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function searchForm()
    {
        $form = $this->createFormBuilder(null)
            ->add('term', TextType::class)
            ->getForm();

        return $this->render('search/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public static function call()
    {
        $connection = $this->getDoctrine()->getManager();
    }
}
