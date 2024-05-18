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
        $basePath = __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/1';
        $fileTypes = ['photos', 'videos'];
        foreach ($fileTypes as $fileType) {
            $storagePath = $basePath . '/' . $fileType;
            if (!file_exists($storagePath)) {
                mkdir($storagePath, recursive: true);
            }
        }

        // create static testing token entity
        $token = '853bc196bb6bdf5f72c33e1eeeb8a8e2';

        $media = new Media();
        $media->setName('test');
        $media->setGalleryName('testing gallery');
        $media->setType('image/png');
        $media->setLength('01:00');
        $media->setOwnerId(1);
        $media->setToken($token);
        $media->setUploadTime(date('d.m.Y H:i:s'));
        $media->setLastEditTime('non-edited');

        // copy testing image
        copy(__DIR__ . '/assets/test.png', __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/1/photos/' . $token . '.png');
        $manager->persist($media);

        // generate 6 images
        for ($i = 1; $i <= 5; $i++) {
            // generate media token
            $token = bin2hex(random_bytes(16));

            $media = new Media();
            $media->setName('test' . $i . '.jpg');
            $media->setGalleryName('testing gallery');
            $media->setType('image/jpg');
            $media->setLength('01:00');
            $media->setOwnerId(1);
            $media->setToken($token);
            $media->setUploadTime(date('d.m.Y H:i:s'));
            $media->setLastEditTime('non-edited');

            // copy testing image
            copy(__DIR__ . '/assets/test.png', __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/1/photos/' . $token . '.png');

            $manager->persist($media);
        }

        // generate 6 videos
        for ($i = 1; $i <= 5; $i++) {
            // generate media token
            $token = bin2hex(random_bytes(16));

            $media = new Media();
            $media->setName('test' . $i . '.mp4');
            $media->setGalleryName('testing gallery');
            $media->setType('video/mp4');
            $media->setLength('01:00');
            $media->setOwnerId(1);
            $media->setToken($token);
            $media->setUploadTime(date('d.m.Y H:i:s'));
            $media->setLastEditTime('non-edited');

            // copy testing video
            copy(__DIR__ . '/assets/test.mp4', __DIR__ . '/../../storage/' . $_ENV['APP_ENV'] . '/1/videos/' . $token . '.mp4');

            $manager->persist($media);
        }

        // save data to database
        $manager->flush();
    }
}
