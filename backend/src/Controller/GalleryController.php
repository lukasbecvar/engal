<?php

namespace App\Controller;

use App\Entity\Media;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class GalleryController extends AbstractController
{
    #[Route('/api/gallery/list', name: 'gallery_list', methods: ['GET'])]
    public function index(Security $security, EntityManagerInterface $entityManager, UserManager $userManager): JsonResponse
    {
        $queryBuilder = $entityManager->createQueryBuilder();

        $galleryNames = $queryBuilder
            ->select('DISTINCT m.gallery_name')
            ->where('m.owner_id = :user_id')
            ->setParameter('user_id', $userManager->getUserData($security)->getId())
            ->from(Media::class, 'm')
            ->getQuery()
            ->getResult();

        $galleryNamesArray = [];
        foreach ($galleryNames as $name) {
            $galleryNamesArray[] = $name['gallery_name'];
        }

        return $this->json([
            'gallery_names' => $galleryNamesArray
        ], 200);
    }
}
