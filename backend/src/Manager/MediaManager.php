<?php

namespace App\Manager;

class MediaManager
{
    private LogManager $logManager;
    private UserManager $userManager;

    public function __construct(LogManager $logManager, UserManager $userManager)
    {
        $this->logManager = $logManager;
        $this->userManager = $userManager;
    }

    public function mediaUpload(string $token, string $gallery, array $uploaded_file): array 
    {
        // upload storage directory
        $storage_directory = __DIR__.'/../../'.$_ENV['STORAGE_DIR_NAME'].'/';

        // list of allowend media fromats
        $allowed_formats = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];

        // get maximal allowed file size from config
        $max_file_size_value = intval($_ENV['MAX_FILE_SIZE']);

        // calculate maximal file size
        $max_file_size = $max_file_size_value * 1024 * 1024;

        // get username (who makes upload)
        $username = $this->userManager->getUsernameByToken($token);

        // create storage dir
        if (!file_exists($storage_directory)) {
            mkdir($storage_directory);
        }

        // create user path 
        if (!file_exists($storage_directory.$username)) {
            mkdir($storage_directory.$username);
        }

        // create gallery dir
        if (!file_exists($storage_directory.$username.'/'.$gallery)) {
            mkdir($storage_directory.$username.'/'.$gallery);
        }

        // check if storage is writable
        if (!is_writable($storage_directory)) {
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
            $destination = $storage_directory.$username.'/'.$gallery.'/'.$file_name;
                                        
            // move file to upload dir
            move_uploaded_file($uploaded_file['tmp_name'], $destination);
                            
            // log action
            $this->logManager->log('uploader', 'user: '.$username.' upload new media: '.$file_name.' to gallery: '.$gallery);
                        
            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Image uploaded to gallery: '.$gallery
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Error to upload image: '.$e->getMessage()
            ];
        }
    }
}
