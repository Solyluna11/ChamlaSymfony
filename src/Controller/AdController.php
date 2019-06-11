<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;




class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

  /**
   * permet de créer une annonce
   *
   * @Route("/ads/new", name = "ads_create")
   *
   * @return Response
   */

   public function  create(Request $request, ObjectManager $manager){
     $ad = new Ad();

     $form =  $this->createForm(AnnonceType::class, $ad);

     $form ->handleRequest($request);

     if($form->isSubmitted() && $form->isValid()){
       foreach ($ad->getImages() as $image){
         $image->setAd($ad);
         $manager->persist($image);
       }

       $ad->setAuthor($this->getUser());

       $manager->persist($ad);
       $manager->flush();

       $this->addFlash(
           'success',
           "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée ! "
       );

       return $this->redirectToRoute('ads_show', [
           'slug' => $ad->getSlug()
       ]);
     }

    return $this->render('ad/new.html.twig',[
        'form' => $form->createView()
    ]);

  }

  /**
   * permet de modifier une annonce existante
   *
   * @Route("/ads/{slug}/edit", name = "ads_edit")
   * @param Ad $ad
   * @param Request $request
   * @return Response
   */

   public function edit(Ad $ad, Request $request, ObjectManager $manager){

      $form =  $this->createForm(AnnonceType::class, $ad);

      $form ->handleRequest($request);

     if($form->isSubmitted() && $form->isValid()){
       foreach ($ad->getImages() as $image){
         $image->setAd($ad);
         $manager->persist($image);
       }

       $manager->persist($ad);
       $manager->flush();

       $this->addFlash(
           'success',
           "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées ! "
       );

       return $this->redirectToRoute('ads_show', [
           'slug' => $ad->getSlug()
       ]);
     }

     return $this->render('ad/edit.html.twig', [
         'form' => $form->createView(),
         'ad' => $ad
      ]);
    }

  /**
   * permet d'afficher une seule annonce
   *
   * @Route("/ads/{slug}", name = "ads_show")
   *
   * @return Response
   */
    public function  show(Ad $ad){

      return $this->render('ad/show.html.twig', [
          'ad'=> $ad

      ]);

    }

}
