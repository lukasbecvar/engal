<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Manager\StorageManager;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class MediaFixtures
 *
 * Fixture class for generating sample media data
 *
 * @package App\DataFixtures
 */
class MediaFixtures extends Fixture
{
    private StorageManager $storageManager;

    public function __construct(StorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
    }

    /**
     * Load method to generate sample media data
     *
     * @param ObjectManager $manager The object manager instance
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
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

        // save testing file
        $testingImage = file_get_contents(__DIR__ . '/assets/test.png');
        $this->storageManager->storeRawContent(1, 'photos', $token, 'png', 'image/png', $testingImage);

        // save data to database
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

            // save testing file
            $testingImage = file_get_contents(__DIR__ . '/assets/test.png');
            $this->storageManager->storeRawContent(1, 'photos', $token, 'png', 'image/png', $testingImage);

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

            // save testing file
            $testingImage = file_get_contents(__DIR__ . '/assets/test.mp4');
            $this->storageManager->storeRawContent(1, 'videos', $token, 'mp4', 'video/mp4', $testingImage);

            $manager->persist($media);
        }

        // save data to database
        $manager->flush();
    }
}
