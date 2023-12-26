<?php

namespace App\Manager;

use App\Util\SiteUtil;
use App\Util\SystemUtil;
use App\Util\SecurityUtil;

/**
 * Class StorageManager
 * @package App\Manager
 */
class StorageManager
{
    /**
     * @var SiteUtil $siteUtil The site utility.
     */
    private SiteUtil $siteUtil;

    /**
     * @var LogManager $logManager The log manager.
     */
    private LogManager $logManager;

    /**
     * @var SystemUtil $systemUtil The OS system utils.
     */
    private SystemUtil $systemUtil;

    /**
     * @var UserManager $userManager The user manager.
     */
    private UserManager $userManager;

    /**
     * @var SecurityUtil $securityUtil The security utility.
     */
    private SecurityUtil $securityUtil;

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
     * @param SiteUtil $siteUtil The site utility.
     * @param LogManager $logManager The log manager.
     * @param SystemUtil $systemUtil The user manager.
     * @param UserManager $userManager The user manager.
     * @param SecurityUtil $securityUtil The security utility.
     * @param ErrorManager $errorManager The error manager.
     */
    public function __construct(
        SiteUtil $siteUtil,
        LogManager $logManager, 
        SystemUtil $systemUtil,
        UserManager $userManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
        $this->systemUtil = $systemUtil;
        $this->userManager = $userManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;

        // init file storage directory (in app root)
        $this->storage_directory = __DIR__.'/../../'.$_ENV['STORAGE_DIR_NAME'];
    }

    /**
     * Upload media file to a specified user's gallery.
     *
     * This method handles the process of uploading a media file to a user's gallery,
     * performing various checks on file format, size, and server storage space.
     *
     * @param string $token The user token for authentication.
     * @param string $gallery The name of the gallery where the media will be uploaded.
     * @param array $uploaded_file The information about the uploaded file (from $_FILES).
     *
     * @return array An associative array with the upload status, code, and message.
     *               - 'status': 'success' or 'error'
     *               - 'code': HTTP status code
     *               - 'message': A descriptive message indicating the result of the upload.
     *
     * @throws \Exception If an unexpected error occurs during the upload process.
     */
    public function mediaUpload(string $token, string $gallery, array $uploaded_file): array 
    {   
        // list of allowend media fromats
        $allowed_formats = explode(',', $_ENV['ALLOWED_MEDIA_FORMATS']);

        // get maximal allowed file size from config
        $max_file_size_value = intval($_ENV['MAX_IMAGE_SIZE']);

        // calculate maximal file size
        $max_file_size = $max_file_size_value * 1024 * 1024;

        // get username (who makes upload)
        $username = $this->userManager->getUsername($token);

        // create storage dir
        if (!file_exists($this->storage_directory)) {
            mkdir($this->storage_directory);
        }

        // create user path 
        if (!file_exists($this->storage_directory.'/'.$username)) {
            mkdir($this->storage_directory.'/'.$username);
        }

        // create gallery dir
        if (!file_exists($this->storage_directory.'/'.$username.'/'.$gallery)) {
            mkdir($this->storage_directory.'/'.$username.'/'.$gallery);
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
        $uploaded_file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
        if (!in_array($uploaded_file_extension, $allowed_formats)) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'unsuported format: allowed formats is: '.$_ENV['ALLOWED_MEDIA_FORMATS']
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

        // check if server size is not reached
        if ($this->systemUtil->getDriveUsage() > 95) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'maximal server storage space is reached, please contact you server admin for fix this problem'
            ];
        }

        try {
            // get file name
            $file_name = $uploaded_file['name'];
            
            // check maximal file name length
            if (strlen($file_name) >= 81) {
                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'maximal file name is 80 characters'
                ];
            }

            // encrypt file name data
            if ($this->siteUtil->isEncryptionEnabled()) {
                $file_name = $this->securityUtil->encryptAES($file_name);
            }

            // build final upload path
            $destination = $this->storage_directory.'/'.$username.'/'.$gallery.'/'.$file_name;

            // check if image exist
            if (file_exists($destination)) {
                
                // decrypt file name
                if ($this->siteUtil->isEncryptionEnabled()) {
                    $file_name = $this->securityUtil->decryptAES($file_name);
                }

                return [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'image: '.$file_name.' is already exist'
                ];

            } else {

                // encrypt file content data
                if ($this->siteUtil->isEncryptionEnabled()) {

                    // get file content
                    $file_content = file_get_contents($uploaded_file['tmp_name']);

                    // encrypt file
                    $encrypted_file = $this->securityUtil->encryptAES($file_content);

                    // save encrypted file
                    file_put_contents($destination, $encrypted_file);
                
                    // delete temp file
                    unlink($uploaded_file['tmp_name']);
                } else {

                    // move file to upload dir
                    move_uploaded_file($uploaded_file['tmp_name'], $destination);
                }

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
     * Get the list of galleries for a specific user.
     *
     * This method retrieves the list of galleries associated with a given username,
     * along with additional information such as the number of images in each gallery
     * and the thumbnail image for preview.
     *
     * @param string $username The username for which to retrieve the gallery list.
     *
     * @return array|null An array containing information about each gallery, or null in case of an error.
     *                   Each gallery information includes:
     *                   - 'name': The name of the gallery.
     *                   - 'images_count': The number of images in the gallery.
     *                   - 'thumbnail': The thumbnail image for preview.
     *
     * @throws \Exception If an error occurs during the process of retrieving the gallery list.
     */
    public function getGalleryListByUsername(string $username): ?array 
    {
        // create storage dir
        if (!file_exists($this->storage_directory)) {
            mkdir($this->storage_directory);
        }
        
        // create user path 
        if (!file_exists($this->storage_directory.'/'.$username)) {
            mkdir($this->storage_directory.'/'.$username);
        }

        try {
            $galleries = scandir($this->storage_directory.'/'.$username);
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
        foreach (glob($this->storage_directory.'/'.$storage_name.'/'.$gallery_name . '/*') as $file) {
            $image_content = file_get_contents($file);
            
            // decrypt thumbnail
            if ($this->siteUtil->isEncryptionEnabled()) {
                $image_content = $this->securityUtil->decryptAES($image_content);
            }

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
        if (file_exists($this->storage_directory.'/'.$storage_name.'/'.$gallery_name)) {
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
            $images = scandir($this->storage_directory.'/'.$storage_name.'/'.$gallery_name);

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
        if (file_exists($this->storage_directory.'/'.$storage_name.'/'.$gallery_name.'/'.$image_name)) {
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
            
            // build image path
            $image_path = $this->storage_directory.'/'.$storage_name.'/'.$gallery_name.'/'.$image_name;
            
            // get image content
            $content = file_get_contents($this->storage_directory.'/'.$storage_name.'/'.$gallery_name.'/'.$image_name);
            
            // decrypt content
            if ($this->siteUtil->isEncryptionEnabled()) {
                $content = $this->securityUtil->decryptAES($content);
            }

            // get image format
            $file_format = pathinfo($image_path, PATHINFO_EXTENSION);
            
            // build image identificator
            $identification = "data:image/$file_format;base64,";
            
            // return image content
            return $identification.base64_encode($content);
        }
        return null;
    }

    /**
     * Rename a user storage directory.
     *
     * This method renames a user storage directory from the specified name to a new name.
     *
     * @param string $storage_name The current name of the user storage directory.
     * @param string $new_storage_name The new name to which the user storage directory should be renamed.
     *
     * @throws \Exception If an error occurs during the renaming process, an exception is caught and logged.
     *
     * @return void
     */
    public function renameStorage(string $storage_name, string $new_storage_name): void 
    {
        // check if file exist
        if (file_exists($this->storage_directory.'/'.$storage_name)) {
            try {

                // rename user storage
                rename($this->storage_directory.'/'.$storage_name, $this->storage_directory.'/'.$new_storage_name);
            } catch (\Exception $e) {
                $this->errorManager->handleError('error to rename user storage: '.$storage_name.' -> '.$new_storage_name.', error: '.$e->getMessage(), 500);
            }
        }
    }
}
