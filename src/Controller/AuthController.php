<?php

namespace App\Controller;

// misc
use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// authentication
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

// session
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthController extends BaseController
{
    

    /**
     * Returns login page rendered html or home page for authenticated users
     * @Route("/", name="loginOrHome")
     */
    public function authOrHome(SessionInterface $session, Request $request) {
        // todo check if already in active session

        // authenticated users will be redirected to home
        $user = $this->getUser();
        if ($this->getUser() instanceof User) {
            return $this->render('user/home.html.twig', ['userName' => $user->getUsername()]);
        }
        
        // anonymus users get login page
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/register/{type}", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $type) {
        // if not valid route throw exception
        $this->enforceRouteParams($type, [User::USER_TYPE_USER, User::USER_TYPE_AGENT]);

        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setCreatedAt(new \DateTime());
            $user->setRole(User::resolveDefaultRoleForType($type));

            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('loginOrHome');
        }

        return $this->render(
            'auth/register.html.twig', [
                'form' => $form->createView(),
                'userType' => $type
            ]
        );
    }

    /**
     * @Route("/user/home", name="home")
     */
    public function home(Request $request) {
        print_r($this->getUser()->getEmail());
        return $this->render('user/home.html.twig', [
            'userName' => $request->getUser()
        ]);
    }
}
