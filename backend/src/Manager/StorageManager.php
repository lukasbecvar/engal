<?php

namespace App\Manager;

/**
 * Class StorageManager
 * @package App\Manager
 */
class StorageManager
{
    /**
     * @var LogManager $logManager The log manager.
     */
    private LogManager $logManager;

    /**
     * @var UserManager $userManager The user manager.
     */
    private UserManager $userManager;

    /**
     * @var ErrorManager $errorManager The error manager.
     */
    private ErrorManager $errorManager;

    /**
     * @var string $storage_directory The base directory for storage.
     */
    private string $storage_directory;

    /**
     * StorageManager constructor.
     * @param LogManager $logManager The log manager.
     * @param UserManager $userManager The user manager.
     * @param ErrorManager $errorManager The error manager.
     */
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

    /**
     * Handles the media upload operation.
     *
     * @param string $token The user token.
     * @param string $gallery The gallery name.
     * @param array $uploaded_file The uploaded file details.
     * @return array The result of the upload operation.
     */
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
                'message' => 'upload error: storage is not writable'
            ];
        }

        // check gallery name minimal length
        if (strlen($gallery) <= 3) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'minimal gallery name length is 4 characters'
            ];
        }

        // check gallery name maximal length
        if (strlen($gallery) >= 31) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'maximal gallery name length is 30 characters'
            ];
        }

        // check if media format allowed
        if (!in_array($uploaded_file['type'], $allowed_formats)) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'unsuported format: allowed formats is: jpg, jpeg, png, gif'
            ];
        }
        
        // check file size limit
        if ($uploaded_file['size'] > $max_file_size) {
            return [
                'status' => 'error',
                'code' => 200,
                'message' => 'maximal file size is '.$max_file_size_value.'MB'
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
                    'message' => 'image: '.$file_name.' is already exist'
                ];

            } else {
                // move file to upload dir
                move_uploaded_file($uploaded_file['tmp_name'], $destination);
                                
                // log action
                $this->logManager->log('uploader', 'user: '.$username.' upload new media: '.$file_name.' to gallery: '.$gallery);

                return [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'image uploaded to gallery: '.$gallery
                ];
            }
                                        
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'error to upload image: '.$e->getMessage()
            ];
        }
    }

    /**
     * Gets the list of galleries for a given username.
     *
     * @param string $username The username.
     * @return array|null The list of galleries or null in case of an error.
     */
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
                    'images_count' => count($this->getImageListWhereGallery($username, $value)),
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

    /**
     * Gets the base64-encoded thumbnail for a given gallery.
     *
     * @param string $storage_name The storage name.
     * @param string $gallery_name The gallery name.
     * @return string|null The base64-encoded thumbnail or null if not found.
     */
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

    /**
     * Checks if a gallery exists.
     *
     * @param string $storage_name The storage name.
     * @param string $gallery_name The gallery name.
     * @return bool True if the gallery exists, false otherwise.
     */
    public function checkIfGalleryExist(string $storage_name, string $gallery_name): bool
    {
        if (file_exists($this->storage_directory.$storage_name.'/'.$gallery_name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the list of images for a given gallery.
     *
     * @param string $storage_name The storage name.
     * @param string $gallery_name The gallery name.
     * @return array|null The list of images or null if the gallery doesn't exist.
     */
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

    /**
     * Checks if an image exists in a given gallery.
     *
     * @param string $storage_name The storage name.
     * @param string $gallery_name The gallery name.
     * @param string $image_name The image name.
     * @return bool True if the image exists, false otherwise.
     */
    public function checkIfImageExist(string $storage_name, string $gallery_name, string $image_name): bool
    {
        if (file_exists($this->storage_directory.$storage_name.'/'.$gallery_name.'/'.$image_name)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the base64-encoded content of an image.
     *
     * @param string $storage_name The storage name.
     * @param string $gallery_name The gallery name.
     * @param string $image_name The image name.
     * @return string|null The base64-encoded content or null if the image doesn't exist.
     */
    public function getImageContent(string $storage_name, string $gallery_name, string $image_name): ?string
    {
        if ($this->checkIfImageExist($storage_name, $gallery_name, $image_name)) {
            $content = file_get_contents($this->storage_directory.$storage_name.'/'.$gallery_name.'/'.$image_name);
            return base64_encode($content);
        }
        return null;
    }
}
