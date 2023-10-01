<?php

namespace App\Controller;

use App\Form\ImageUploadType;
use App\Helper\ErrorHelper;
use App\Helper\LogHelper;
use App\Helper\LoginHelper;
use App\Util\EscapeUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\ByteString;

class UploadController extends AbstractController
{

    private $loginHelper;
    private $erorHelper;
    private $logHelper;

    public function __construct(LoginHelper $loginHelper, ErrorHelper $erorHelper, LogHelper $logHelper)
    {
        $this->loginHelper = $loginHelper;
        $this->erorHelper = $erorHelper;
        $this->logHelper = $logHelper;
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
                
                // build upload path
                $path = __DIR__.'/../../storage/'.$this->loginHelper->getUsername().'/';

                // get form data
                $uploaded_images = $form->get('images')->getData();
                $image_name = EscapeUtil::special_chars_strip($form->get('imageName')->getData());
                $gallery_name = EscapeUtil::special_chars_strip($form->get('galleryName')->getData());

                // upload all images
                foreach ($uploaded_images as $file) {

                    // check if image name is empty
                    if (empty($image_name)) {
                        $final_name = ByteString::fromRandom(16)->toString().'.image';
                    } else {

                        // check if upload multiple
                        if (count($uploaded_images) > 1) {

                            // generate final name with prefix
                            $final_name = $image_name.'_'.ByteString::fromRandom(6)->toString().'.image';
                        } else {

                            // get final name name
                            $final_name = $image_name.'.image';
                        }
                    }

                    // get image content
                    $fileContents = file_get_contents($file);

                    // encode image
                    $image_code = base64_encode($fileContents);

                    // build final path
                    $final_path = $path.$gallery_name.'/'.$final_name;

                    // check if gallery exist 
                    if (!is_dir($path.$gallery_name)) {

                        // create gallery dir
                        mkdir($path.$gallery_name);
                    }

                    // save file & check if valid
                    if (file_exists($final_path)) {
                        return $this->render('upload.html.twig', [
                            'form' => $form->createView(),
                            'errorMSG' => 'image: '.$final_name.' is exist!'
                        ]);
                    } else {

                        // upload file & check if valid
                        if (file_put_contents($final_path, $image_code) !== false) {
                            
                            // log upload action to database
                            $this->logHelper->log('upload', 'user: '.$this->loginHelper->getUsername().' uploaded image: '.$final_name.' in gallery: '.$gallery_name);
                        } else {
                            $this->erorHelper->handleError('error to upload file: '.$final_name.' to gallery: '.$gallery_name, 500);
                        }
                    }
                }

                return $this->redirectToRoute('app_gallery', [
                    'name' => $gallery_name,
                    'page' => 1
                ]);
            }
    
            // get upload page
            return $this->render('upload.html.twig', [
                'form' => $form->createView(),
                'errorMSG' => null
            ]);

        }    
    }
}
