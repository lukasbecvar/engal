<?php

namespace App\Controller; 

use App\Entity\User;
use App\Form\RegisterFormType;
use App\Helper\EntityHelper;
use App\Helper\LogHelper;
use App\Util\EscapeUtil;
use App\Util\VisitorInfoUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\ByteString;

/*
    Register controller provides user registration
*/

class RegisterController extends AbstractController
{

    private $logHelper;
    private $entityHelper;
    private $entityManager;

    public function __construct(
        LogHelper $logHelper,
        EntityHelper $entityHelper,
        EntityManagerInterface $entityManager
    ){
        $this->logHelper = $logHelper;
        $this->entityHelper = $entityHelper;
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function index(Request $request): Response
    {
        // create user instance
        $user = new User();

        // create register form
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        // check if submited
        if ($form->isSubmitted() && $form->isValid()) {

            // get form data
            $username = $form->get('username')->getData();
            $password = $form->get('password')->getData();
            $repassword = $form->get('re-password')->getData();

            // escape values (XSS protection)
            $username = EscapeUtil::special_chars_strip($username);
            $password = EscapeUtil::special_chars_strip($password);
            $repassword = EscapeUtil::special_chars_strip($repassword);

            // check if username used
            if ($this->entityHelper->isEntityExist('username', $username, $user)) {
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

            // set from data
            $user->setUsername($username);
            $user->setPassword($password);

            // generate token
            $token = ByteString::fromRandom(32)->toString();

            // get user ip
            $ipAddress = VisitorInfoUtil::getIP();

            // set others
            $user->setToken($token);
            $user->setRole('user');
            $user->setIpAddress($ipAddress);

            // log regstration event
            $this->logHelper->log('registration', 'new user: $username registred');

            // insert new user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // return login page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register.html.twig', [
            'errorMSG' => null,
            'registrationForm' => $form->createView(),
        ]);
    }
}
