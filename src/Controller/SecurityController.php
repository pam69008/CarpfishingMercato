<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route ("/profile", name = "profile")
     * @return Response
     */
    public function getProfile() {
        $userLog = $this->getUser();
        if ($userLog === null)
        {
            throw new Exception('This user doesnt exist');
        }

        if ($userLog->isCompany() === false) {
            return $this->render('profile/my_profile.html.twig', ['user' => $userLog]);
        } else {
            return $this->render('profile/company_profile.html.twig', ['user' => $userLog]);
        }
    }

    /**
     * @Route ("/profile/{id}/edit", name = "modify_profile")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function modifyProfile(Request $request, User $user): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['pictures']->getData();
            if ($file) {
                $fileName = 'Image' .$user->getId() . '.' . $file->guessExtension();
//                 moves the file to the directory where brochures are stored
                $destination = $this->getParameter('image_user_upload');
                $file->move(
                    $destination,
                    $fileName
                );

                // updates the 'brochure' property to store the PDF file name
                // instead of its contents
                $user->setPictureName($fileName);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
