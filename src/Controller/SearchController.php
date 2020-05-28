<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\{TextType};


class SearchController extends AbstractController
{
    /**
     * @Route("/", name="search")
     */
    public function index(Request $response)
    {
        // Check if there is a POST request
        $search_term = $response->request->get('form');

        if (!empty($search_term)) {
            $term = trim($search_term['term']);

            // First check if term already exists if database
    

            $response = $this->call($term);
            var_dump($response); exit;
        }

        $form = $this->createFormBuilder(null)
            ->add('term', TextType::class)
            ->getForm();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'form' => $form->createView(),
        ]);
    }

    public static function call($term)
    {
        $url = "https://api.github.com/search/issues";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_POSTFIELDS => "q={$term}",
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }    
}
