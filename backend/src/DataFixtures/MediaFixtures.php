<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class MediaFixtures
 * 
 * Fixture class for generating sample media data.
 * 
 * @package App\DataFixtures
 */
class MediaFixtures extends Fixture
{
    /**
     * Load method to generate sample media data.
     *
     * @param ObjectManager $manager The object manager instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        // prepere test storage path
        $base_path = __DIR__.'/../../storage/'.$_ENV['APP_ENV'].'/1';
        $file_types = ['photos', 'videos'];
        foreach ($file_types as $file_type) {
            $storage_path = $base_path.'/'.$file_type;
            if (!file_exists($storage_path)) {
                mkdir($storage_path, recursive: true);
            }
        }

        // generate 6 images
        for ($i = 1; $i <= 5; $i++) {
            // generate media token
            $token = bin2hex(random_bytes(16));

            $media = new Media();
            $media->setName('test'.$i.'.jpg');
            $media->setGalleryName('gegreggr');
            $media->setType('image/jpg');
            $media->setOwnerId(1);
            $media->setToken($token);
            $media->setUploadTime(date('d.m.Y H:i:s'));
            $media->setLastEditTime('non-edited');

            // copy testing image
            copy(__DIR__.'/assets/test.png', __DIR__.'/../../storage/'.$_ENV['APP_ENV'].'/1/photos/'.$token.'.png');

            $manager->persist($media);
        }

        // generate 6 videos
        for ($i = 1; $i <= 5; $i++) {
            // generate media token
            $token = bin2hex(random_bytes(16));

            $media = new Media();
            $media->setName('test'.$i.'.mp4');
            $media->setGalleryName('gegreggr');
            $media->setType('video/mp4');
            $media->setOwnerId(1);
            $media->setToken($token);
            $media->setUploadTime(date('d.m.Y H:i:s'));
            $media->setLastEditTime('non-edited');

            // copy testing video
            copy(__DIR__.'/assets/test.mp4', __DIR__.'/../../storage/'.$_ENV['APP_ENV'].'/1/videos/'.$token.'.mp4');

            $manager->persist($media);
        }

        // save data to database
        $manager->flush();
    }
}
