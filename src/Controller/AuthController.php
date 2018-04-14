<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends BaseController
{
    const USER = 'user';
    const AGENT = 'agent';

    /**
     * @Route("/", name="auth")
     */
    public function index()
    {
        return $this->render('auth/index.html.twig');
    }

    /**
     * Returns login page rendered html
     * @Route("/login", methods="GET", name="login_page")
     */
    public function loginPage(Request $request) {
        // todo check if already in active session

        // return rendered login page
        return $this->render('auth/login.html.twig');
    }

    /**
     * Register action, redirect to user page on success
     * @Route("/register/{type}", methods="GET", name="login")
     */
    public function registerPage(Request $request, string $type) {
        // if not valid route throw exception
        $this->enforceRouteParams($type, [self::USER, self::AGENT]);

        // return rendered login page
        return $this->render('auth/login.html.twig', [
            'userType' => $type,
        ]);
    }

    /**
     * Login action, redirect to user page on success
     * @Route("/login", methods="POST", name="login")
     */
    public function login(Request $request) {

    }


}
