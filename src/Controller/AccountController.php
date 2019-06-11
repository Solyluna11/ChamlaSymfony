<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
  /**
   * @Route("/login", name="account_login")
   * @return Response
   */
  public function login(AuthenticationUtils $utils)
  {
    $error = $utils->getLastAuthenticationError();
    $username = $utils->getLastUsername();

    return $this->render('account/login.html.twig',[
        'hasError' => $error !== null,
        'username' => $username
    ]);
  }

  /**
   * @Route("/logout", name="account_logout")
   * @return void
   */
  public function logout(){

  }

  /**
   * @Route("/register", name="account_register")
   * @return Response
   */
  public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
    $user = new User();

    $form = $this->createForm(RegistrationType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
      $hash = $encoder->encodePassword($user, $user->getHash());
      $user->setHash($hash);

      $manager->persist($user);
      $manager->flush();

      $this->addFlash(
          'success',
          "Votre compte a bien été crée ! Vous pouvez maintenant vous connecter !"
      );
      return $this->redirectToRoute('account_login');
    }
    return $this->render('account/registration.html.twig', [
        'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/account/profile", name="account_profile")
   * @return Response
   */
  public function profile(Request $request, ObjectManager $manager){
    $user = $this->getUser();

    $form = $this->createForm(AccountType::class, $user);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $manager->persist($user);
      $manager->flush();
      dump($user);
      $this->addFlash(
          'success',
          "Les données du profil on été enregistée avec succès !"
      );
    }
    return $this->render('account/profile.html.twig', [
        'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/account/password-update", name="account_password")
   * @return Response
   */
  public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager)
  {
    $passwordUpdate = new PasswordUpdate();
    $user = $this->getUser();
    $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
      if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())){
        $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
      }else{
        $newPassword = $passwordUpdate->getNewPassword();
        $hash = $encoder->encodePassword($user, $newPassword);

        $user->setHash($hash);

        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre mot de passe a bien été modifié !"
        );
        return $this->redirectToRoute('homepage');
      }
    };
    return $this->render('account/password.html.twig',[
        'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/account", name="account_index")
   * @return Response
   */
  public function myAccount(){
    return $this->render('user/index.html.twig', [
        'user' => $this->getUser()
    ]);
  }
}