<?php

namespace App\Controller; 

use App\Entity\User;
use App\Util\EscapeUtil;
use App\Helper\LogHelper;
use App\Helper\HashHelper;
use App\Helper\LoginHelper;
use App\Helper\EntityHelper;
use App\Util\VisitorInfoUtil;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/*
    Register controller provides user registration
*/

class RegisterController extends AbstractController
{

    private $logHelper;
    private $hashHelper;
    private $loginHelper;
    private $entityHelper;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper,
        HashHelper $hashHelper,
        LoginHelper $loginHelper,
        EntityHelper $entityHelper,
        EntityManagerInterface $entityManager
    ) {
        $this->logHelper = $logHelper;
        $this->hashHelper = $hashHelper;
        $this->loginHelper = $loginHelper;
        $this->entityHelper = $entityHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {

        // check if user logged in
        if ($this->loginHelper->isUserLogedin()) {
            return $this->redirectToRoute('app_home');   
        }

        // check if register enabled
        if ($_ENV['REGISTER_ENABLED'] == 'false') {
            return $this->render('regsiter-disabled.html.twig');
        }

        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        // check if submited
        if ($form->isSubmitted() && $form->isValid()) {

            // get current date
            $date = date('d.m.Y H:i:s');

            // get form data
            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();
            $repassword = $form->get('re-password')->getData();

            // escape values (XSS protection)
            $username = EscapeUtil::special_chars_strip($username);
            $password = EscapeUtil::special_chars_strip($password);
            $repassword = EscapeUtil::special_chars_strip($repassword);

            // check if username used
            if ($this->entityHelper->isEntityExist(['username' => $username], $user)) {
                return $this->render('register.html.twig', [
                    'errorMSG' => 'This username is already in use',
                    'registrationForm' => $form->createView(),
                ]);
            }

            // check if not match
            if ($password != $repassword) {
                return $this->render('register.html.twig', [
                    'errorMSG' => 'Your passwords dont match',
                    'registrationForm' => $form->createView(),
                ]);
            }

            // password hash
            $hashedPassword = $this->hashHelper->gen_bcrypt($password, 10);

            // set from data
            $user->setUsername($username);
            $user->setPassword($hashedPassword);

            // set login date
            $user->setFirstLogin($date);
            $user->setLastLogin('not logged');

            // generate token
            $token = ByteString::fromRandom(32)->toString();

            // get user ip
            $ipAddress = VisitorInfoUtil::getIP();

            // set others
            $user->setToken($token);
            $user->setRole("user");
            $user->setIpAddress($ipAddress);

            // log regstration event
            $this->logHelper->log('authenticator', 'registration new user: '.$username.' registred');

            // insert new user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set user token (login-token session)
            $this->loginHelper->login($username, $user->getToken(), false);

            // redirect to homepage
            return $this->redirectToRoute('app_home');
        }

        return $this->render('register.html.twig', [
            'errorMSG' => null,
            'registrationForm' => $form->createView(),
        ]);
    }
}
