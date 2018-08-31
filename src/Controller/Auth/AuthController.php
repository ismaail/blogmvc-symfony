<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AuthController
 *
 * @package App\Controller\Auth
 */
class AuthController extends AbstractController
{
    /**
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
