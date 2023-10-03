<?php

namespace App\Controller;

use App\Util\EscapeUtil;
use App\Helper\LogHelper;
use App\Helper\ErrorHelper;
use App\Helper\LoginHelper;
use App\Util\EncryptionUtil;
use App\Form\ImageUploadType;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController
{

    private $logHelper;
    private $erorHelper;
    private $loginHelper;

    public function __construct(
        LogHelper $logHelper,
        ErrorHelper $erorHelper, 
        LoginHelper $loginHelper 
    ) {
        $this->logHelper = $logHelper;
        $this->erorHelper = $erorHelper;
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
                
                // build upload path
                $path = __DIR__.'/../../storage/'.$this->loginHelper->getUsername().'/';

                // get form data
                $uploaded_images = $form->get('images')->getData();
                $image_name = EscapeUtil::special_chars_strip($form->get('imageName')->getData());
                $gallery_name = EscapeUtil::special_chars_strip($form->get('galleryName')->getData());
                $gallery_name_new = EscapeUtil::special_chars_strip($form->get('newGalleryName')->getData());

                // check if new gallery submit
                if (!empty($gallery_name_new)) {
                    $gallery_name = $gallery_name_new;
                }

                // check if gallery name is Add-new/
                if ($gallery_name == 'Add-new') {
                    return $this->render('upload.html.twig', [
                        'form' => $form->createView(),
                        'errorMSG' => 'please enter gallery name or select existing gallery'
                    ]);                     
                }

                // check if maximal images reached
                if (count($uploaded_images) > 40) {
                    return $this->render('upload.html.twig', [
                        'form' => $form->createView(),
                        'errorMSG' => 'maximal 40 images allowed'
                    ]);                   
                } 

                // upload all images
                foreach ($uploaded_images as $file) {

                    // get file extension
                    $extension = $file->getClientOriginalExtension();

                    // check if file is image
                    if (
                        $extension == "gif" or 
                        $extension == "jpg" or 
                        $extension == "jpeg" or 
                        $extension == "jfif" or 
                        $extension == "pjpeg" or 
                        $extension == "pjp" or 
                        $extension == "png" or 
                        $extension == "webp" or 
                        $extension == "bmp" or 
                        $extension == "ico"
                    ) {
                    
                        // check if image name is empty
                        if (empty($image_name)) {

                            // get file name
                            $file_name = $file->getClientOriginalName();

                            // remove file extension from name
                            $file_name = strstr($file_name, '.', true);

                            // build file name
                            $final_name = $file_name.'.image';
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

                        if (EncryptionUtil::isEnabled()) {
                            $image_code = EncryptionUtil::encrypt_aes($image_code);
                        }

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
                    } else {
     
                        return $this->render('upload.html.twig', [
                            'form' => $form->createView(),
                            'errorMSG' => 'the input file is not an image'
                        ]);                       
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
