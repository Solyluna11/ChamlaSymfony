<?php
/**
 * Created by PhpStorm.
 * User: laurence
 * Date: 22/03/19
 * Time: 10:41
 */

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/hello/{prenom}", name="hello_prenom")
     * @Route("/hello" , name= "hello_base")
     * Montre la page qui dit bonjour
     */

    public function hello($prenom = "anonyme" ){
        return $this-> render(
            'hello.html.twig',
            [
                'prenom'=>$prenom

            ]
        );
    }

    /**
     * @Route("/", name = "homepage")
     */
    public function home(){
        $prenoms = ["Varin"=>54, "Laurence"=> 40, "Solyluna"=> 37];
        return $this->render(
            'home.html.twig',
            [ 'title'=>"Bonjour à tous",
                'age'=>12,
                'tableau'=>$prenoms
            ]
        );
    }

}