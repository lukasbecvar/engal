<?php

namespace App\Controller;

use App\Form\ImageUploadType;
use App\Helper\LoginHelper;
use App\Util\EscapeUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UploadController extends AbstractController
{

    private $loginHelper;

    public function __construct(LoginHelper $loginHelper)
    {
        $this->loginHelper = $loginHelper;
    }

    #[Route('/upload', name: 'app_upload')]
    public function index(Request $request): Response
    {
        // check if not logged
        if (!$this->loginHelper->isUserLogedin()) {            
            return $this->render('home.html.twig');
        } else {

            // build form
            $form = $this->createForm(ImageUploadType::class);
            $form->handleRequest($request);
    
            // check if form submited
            if ($form->isSubmitted() && $form->isValid()) {
                
                // get form data
                $uploaded_images = $form->get('images')->getData();
                $image_name = EscapeUtil::special_chars_strip($form->get('imageName')->getData());
                $gallry_name = EscapeUtil::special_chars_strip($form->get('galleryName')->getData());
    
                dd(empty($image_name));


                die();
            }
    
            return $this->render('upload.html.twig', [
                'form' => $form->createView(),
            ]);

        }    
    }
}
