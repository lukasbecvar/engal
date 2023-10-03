<?php

namespace App\Helper;

use App\Util\EncryptionUtil;

/*
    Storage helper provides file storage dirctory methods
*/

class StorageHelper
{
    public function createStorage(string $storage_name): void {
        if (!file_exists(__DIR__.'/../../storage')) {
            mkdir(__DIR__.'/../../storage');
        }
        if (!$this->checkStorage($storage_name)) {
            mkdir(__DIR__.'/../../storage/'.$storage_name);
        }
    }

    public function checkStorage(string $storage_name): bool {
        if (file_exists(__DIR__.'/../../storage/'.$storage_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkGallery(string $storage_name, string $gallery_name): bool {
        if (file_exists(__DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name)) {
            return true;
        } else {
            return false;
        }
    }

    public function isGalleryEmpty(string $storage_name, string $gallery_name): bool {
        
        // build gallery path
        $path = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name;

        // get images
        $images = scandir($path);
        
        // remove . & ..
        $images = array_diff($images, ['.', '..']);
        
        // check if empty
        if (empty($images)) {
            return true;
        } else {
            return false;
        }
    }

    public function getThumbnail(string $storage_name, string $gallery_name): ?string {

        $dir = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name;
        $files = glob($dir . '/*.image');
        
        $content = null;

        if ($files !== false && !empty($files)) {
            $firstFile = $files[0];
            if (str_ends_with($firstFile, '.image')) {
                if (is_file($firstFile)) {
                    $fileContents = file_get_contents($firstFile);

                    if (EncryptionUtil::isEnabled()) {
                        $content = EncryptionUtil::decrypt_aes($fileContents);
                    }
                }
            }
        }
        return $content;
    }

    public function getImage(string $storage_name, string $gallery_name, string $image_name): ?string {

        $file = __DIR__.'/../../storage/'.$storage_name.'/'.$gallery_name.'/'.$image_name;
        
        $fileContents = file_get_contents($file);

        if (EncryptionUtil::isEnabled()) {
            $fileContents = EncryptionUtil::decrypt_aes($fileContents);
        }

        return $fileContents;
    }

    public function getGalleryListWithPrefix(string $storage_name): ?array {
        
        $list = [];

        // get all galleries
        $galleries = $this->getGalleries($storage_name);

        // and new row value
        $list['Add new'] = 'Add-new';

        // add all galleries to list 
        foreach ($galleries as $gallery) {
            $list[$gallery['name']] = $gallery['name'];
        }


        return $list;
    }

    public function getGalleries(string $storage_name): ?array {
        if (!$this->checkStorage($storage_name)) {
            $this->createStorage($storage_name);
        }
        $galleries = scandir(__DIR__.'/../../storage/'.$storage_name);
        $galleries = array_diff($galleries, array('..', '.'));

        $arr = [];

        foreach ($galleries as $value) {

            // get gallery thumbnail
            $thumbnail = $this->getThumbnail($storage_name, $value);

            if($this->getImages($storage_name, $value) != null) {
                $gallery = [
                    'name' => $value,
                    'thumbnail' => $thumbnail
                ];
    
                array_push($arr, $gallery);
            }
        }

        return $arr;
    }

    public function getImages(string $storage_name, string $gallery_name): ?array {
        if (!$this->checkStorage($storage_name)) {
            $this->createStorage($storage_name);
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

    public function getImagesContent(string $storage_name, string $gallery_name, int $page) {
        if (!$this->checkStorage($storage_name)) {
            $this->createStorage($storage_name);
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
                        'image' => $this->getImage($storage_name, $gallery_name, $value)
                    ];
                    array_push($arr, $content);
                }
            }
            $i++;
        }

        return $arr;
    }
    
    public function getImagesContentAll(string $storage_name, int $page, string $sort = null): ?array {
        if (!$this->checkStorage($storage_name)) {
            $this->createStorage($storage_name);
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

                    $content = file_get_contents($value);

                    $lastAsteriskPos = strrpos($value, '/');
                    $name = substr($value, $lastAsteriskPos + 1);
                    $name = strstr($name, '.', true);

                    if (EncryptionUtil::isEnabled()) {
                        $content = EncryptionUtil::decrypt_aes($content);
                    }

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
