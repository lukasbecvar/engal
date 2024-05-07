<?php

namespace App\Controller;

use App\Entity\Media;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GalleryController extends AbstractController
{
    #[Route('/api/gallery/list', name: 'gallery_list', methods: ['GET'])]
    public function index(Security $security, EntityManagerInterface $entityManager, UserManager $userManager): JsonResponse
    {
        $query_builder = $entityManager->createQueryBuilder();

        $gallery_names = $query_builder
            ->select('DISTINCT m.gallery_name')
            ->where('m.owner_id = :user_id')
            ->setParameter('user_id', $userManager->getUserData($security)->getId())
            ->from(Media::class, 'm')
            ->getQuery()
            ->getResult();

        $gallery_names_array = [];
        foreach ($gallery_names as $name) {
            $gallery_names_array[] = $name['gallery_name'];
        }

        return $this->json([
            'gallery_names' => $gallery_names_array
        ], 200);
    }
}
