<?php

namespace App\Manager;

class StorageManager
{
    private LogManager $logManager;
    private UserManager $userManager;
    private ErrorManager $errorManager;

    private string $storage_directory;

    public function __construct(
        LogManager $logManager, 
        UserManager $userManager,
        ErrorManager $errorManager
    ) {
        $this->logManager = $logManager;
        $this->userManager = $userManager;
        $this->errorManager = $errorManager;

        // init file storage directory (in app root)
        $this->storage_directory = __DIR__.'/../../'.$_ENV['STORAGE_DIR_NAME'].'/';
    }

    public function mediaUpload(string $token, string $gallery, array $uploaded_file): array 
    {
        // list of allowend media fromats
        $allowed_formats = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

        // get maximal allowed file size from config
        $max_file_size_value = intval($_ENV['MAX_FILE_SIZE']);

        // calculate maximal file size
        $max_file_size = $max_file_size_value * 1024 * 1024;

        // get username (who makes upload)
        $username = $this->userManager->getUsernameByToken($token);

        // create storage dir
        if (!file_exists($this->storage_directory)) {
            mkdir($this->storage_directory);
        }

        // create user path 
        if (!file_exists($this->storage_directory.$username)) {
            mkdir($this->storage_directory.$username);
        }

        // create gallery dir
        if (!file_exists($this->storage_directory.$username.'/'.$gallery)) {
            mkdir($this->storage_directory.$username.'/'.$gallery);
        }

        // check if storage is writable
        if (!is_writable($this->storage_directory)) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Upload error: storage is not writable'
            ];
        }

        // check if media format allowed
        if (!in_array($uploaded_file['type'], $allowed_formats)) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'Unsuported format: allowed formats is: jpg, jpeg, png, gif'
            ];
        }
        
        // check file size limit
        if ($uploaded_file['size'] > $max_file_size) {
            return [
                'status' => 'error',
                'code' => 200,
                'message' => 'Maximal file size is '.$max_file_size_value.'MB'
            ];
        }

        try {
            // get file name
            $file_name = $uploaded_file['name'];
            
            // build final upload path
            $destination = $this->storage_directory.$username.'/'.$gallery.'/'.$file_name;

            // check if image exist
            if (file_exists($destination)) {
                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Image: '.$file_name.' is already exist'
                ];

            } else {
                // move file to upload dir
                move_uploaded_file($uploaded_file['tmp_name'], $destination);
                                
                // log action
                $this->logManager->log('uploader', 'user: '.$username.' upload new media: '.$file_name.' to gallery: '.$gallery);

                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Image uploaded to gallery: '.$gallery
                ];
            }
                                        
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Error to upload image: '.$e->getMessage()
            ];
        }
    }

    public function getGalleryListByUsername(string $username): ?array 
    {
        // create storage dir
        if (!file_exists($this->storage_directory)) {
            mkdir($this->storage_directory);
        }
        
        // create user path 
        if (!file_exists($this->storage_directory.$username)) {
            mkdir($this->storage_directory.$username);
        }

        try {
            $galleries = scandir(__DIR__.'/../../'.$_ENV['STORAGE_DIR_NAME'].'/'.$username);
            $galleries = array_diff($galleries, array('..', '.'));
    
            $arr = [];

            foreach ($galleries as $value) {
                $gallery = [
                    'name' => $value,
                    'thumbnail' => $this->getThumbnail($username, $value)
                ];
                array_push($arr, $gallery);            
            }
    
            return $arr;
        } catch (\Exception $e) {
            $this->errorManager->handleError('error to get gallery list: '.$e->getMessage(), 500);
            return null;
        }
    }

    public function getThumbnail(string $storage_name, string $gallery_name): ?string 
    {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        foreach (glob($this->storage_directory.$storage_name.'/'.$gallery_name . '/*.{'.implode(',', $allowed_extensions).'}', GLOB_BRACE) as $file) {
            $image_content = file_get_contents($file);
            $base64_image = base64_encode($image_content);
            return $base64_image;
        }
        return null;
    }

    public function checkIfGalleryExist(string $storage_name, string $gallery_name): bool
    {
        if (file_exists($this->storage_directory.$storage_name.'/'.$gallery_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function getImageListWhereGallery(string $storage_name, string $gallery_name): ?array
    {
        // check if gallery exist
        if ($this->checkIfGalleryExist($storage_name, $gallery_name)) {

            // get images list from storage
            $images = scandir($this->storage_directory.$storage_name.'/'.$gallery_name);

            // remove dots links
            $images = array_diff($images, array('..', '.'));
            
            return $images;
        }
        return null;
    }

    public function checkIfImageExist(string $storage_name, string $gallery_name, string $image_name): bool
    {
        if (file_exists($this->storage_directory.$storage_name.'/'.$gallery_name.'/'.$image_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function getImageContent(string $storage_name, string $gallery_name, string $image_name): ?string
    {
        if ($this->checkIfImageExist($storage_name, $gallery_name, $image_name)) {
            $content = file_get_contents($this->storage_directory.$storage_name.'/'.$gallery_name.'/'.$image_name);
            return base64_encode($content);
        }
        return null;
    }
}
