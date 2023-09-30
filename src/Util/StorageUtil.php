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

    // get galleryes list
    public static function getGallerys(string $storage_name) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }
        $gallerys = scandir(__DIR__.'/../../storage/'.$storage_name);
        $gallerys = array_diff($gallerys, array('..', '.'));

        $arr = [];

        foreach ($gallerys as $value) {

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

        $images = null;

        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            $images = scandir(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name);
            $images = array_diff($images, array('..', '.'));
        }

        return $images;
    }

    // get images content
    public static function getImagesContent(string $storage_name, string $gallery_name) {
        if (!StorageUtil::checkStorage($storage_name)) {
            StorageUtil::createStorage($storage_name);
        }

        $images = null;

        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            $images = scandir(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name);
            $images = array_diff($images, array('..', '.'));
        }

        $arr = [];

        foreach ($images as $value) {

            $content = [
                'name' => $value,
                'image' => StorageUtil::getImage($storage_name, $gallery_name, $value)
            ];

            array_push($arr, $content);
        }

        return $arr;
    }
    
}
