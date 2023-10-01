<?php

namespace App\Util;

/*
    StorageUtil util provides file storage dirctory methods
*/

class StorageUtil
{
    // create storage dir (if not exist)
    public static function createStorage(string $storage_name) {
        if (!file_exists(__DIR__.'/../../storage')) {
            mkdir(__DIR__.'/../../storage');
        }
        if (!StorageUtil::checkStorage($storage_name)) {
            mkdir(__DIR__.'/../../storage/'.$storage_name);
        }
    }

    // check if storage exist
    public static function checkStorage(string $storage_name) {
        if (file_exists(__DIR__.'/../../storage/'.$storage_name)) {
            return true;
        } else {
            return false;
        }
    }

    // check if gallery exist
    public static function checkGallery(string $storage_name, string $gallery_name) {
        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            return true;
        } else {
            return false;
        }
    }

    // get thumbnail
    public static function getThumbnail(string $storage_name, string $gallery_name) {

        $dir = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name;
        $files = glob($dir . '/*.image');
        
        $content = null;

        if ($files !== false && !empty($files)) {
            $firstFile = $files[0];
            if (str_ends_with($firstFile, '.image')) {
                if (is_file($firstFile)) {
                    $fileContents = file_get_contents($firstFile);
                    $content = nl2br(htmlspecialchars($fileContents)); 
                }
            }
        }
        return $content;
    }

    // get image content
    public static function getImage(string $storage_name, string $gallery_name, string $image_name) {

        $file = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name.'/'.$image_name;
        
        $fileContents = file_get_contents($file);
        $content = nl2br(htmlspecialchars($fileContents)); 
        return $content;
    }

    // get galleries list
    public static function getGalleries(string $storage_name) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }
        $galleries = scandir(__DIR__.'/../../storage/'.$storage_name);
        $galleries = array_diff($galleries, array('..', '.'));

        $arr = [];

        foreach ($galleries as $value) {

            if(StorageUtil::getImages($storage_name, $value) != null) {
                $gallery = [
                    'name' => $value,
                    'thumbnail' => StorageUtil::getThumbnail($storage_name, $value)
                ];
    
                array_push($arr, $gallery);
            }
        }

        return $arr;
    }

    // get images in gallery
    public static function getImages(string $storage_name, string $gallery_name) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }

        $arr = [];

        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            $images = scandir(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name);
            foreach ($images as $image) {
                if (str_ends_with($image, '.image')) {
                    array_push($arr, $image);
                }
            }
            $arr = array_diff($arr, array('..', '.'));
        }

        return $arr;
    }

    // get images content
    public static function getImagesContent(string $storage_name, string $gallery_name, int $page) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }

        $images = [];

        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            $images = scandir(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name);
        }  

        $images = array_diff($images, array('..', '.'));

        $arr = [];

        // sort normal
        natsort($images);

        // get limit from config
        $limit = $_ENV['LIMIT_PER_PAGE']; 
        // calculate content range
        $start_index = ($page - 1) * $limit;
        $end_index = $start_index + $limit - 1;

        // fix for first page
        if ($page == 0) {
            $start_index = $start_index - 1;
            $end_index = $end_index - 1;
        }

        $i = 0;
        foreach ($images as $value) {

            // check if start & end index is valud
            if ($start_index <= $i && $end_index >= $i) {
                if (str_ends_with($value, '.image')) {
                    $name = strstr($value, '.', true);
                    $content = [
                        'name' => $name,
                        'image' => StorageUtil::getImage($storage_name, $gallery_name, $value)
                    ];
                    array_push($arr, $content);
                }
            }
            $i++;
        }

        return $arr;
    }
    
    // get images content (all images)
    public static function getImagesContentAll(string $storage_name, int $page, string $sort = null) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }

        $pattern = __DIR__.'/../../storage/'.$storage_name.'/*';
        
        $images = [];
        
        // get all gallery folders by pattern
        $folders = glob($pattern, GLOB_ONLYDIR);
        
        $files = [];
        
        // get files from all gallerys
        foreach ($folders as $folder) {
            $folderFiles = glob($folder . '/*');
            $files = array_merge($files, $folderFiles);
        }
        
        // save files to array
        foreach ($files as $file) {
            array_push($images, $file);
        }

        $images = array_diff($images, array('..', '.'));

        if ($sort == 'random_sort') {
            shuffle($images);
        }

        $arr = [];

        // get limit from config
        $limit = $_ENV['LIMIT_PER_PAGE']; 
        // calculate content range
        $start_index = ($page - 1) * $limit;
        $end_index = $start_index + $limit - 1;

        // fix for first page
        if ($page == 0) {
            $start_index = $start_index - 1;
            $end_index = $end_index - 1;
        }

        $i = 0;
        foreach ($images as $value) {

            // check if start & end index is valud
            if ($start_index <= $i && $end_index >= $i) {
                if (str_ends_with($value, '.image')) {

                    $fileContents = file_get_contents($value);
                    $content = nl2br(htmlspecialchars($fileContents)); 

                    $lastAsteriskPos = strrpos($value, '/');
                    $name = substr($value, $lastAsteriskPos + 1);
                    $name = strstr($name, '.', true);

                    $content = [
                        'name' => $name,
                        'image' => $content
                    ];
                    array_push($arr, $content);
                }
            }
            $i++;
        }

        return $arr;
    }
}
