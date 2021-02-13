<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
/**
 * Class CategoryController.
 */
#[Route('/api/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'category_list', methods: ['GET'])]
    public function list(CategoryRepository $categoryRepository): Response
    {
        return $this->json($categoryRepository->findAll(), Response::HTTP_OK, [], [
            'groups' => 'list'
        ]);
    }

    #[Route('/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(
        EntityManagerInterface $entityManager,
        Request $request,
    SerializerInterface $serializer,
    ValidatorInterface $validator): Response
    {

        $content = $request->getContent();

        try {
            $category = $serializer->deserialize($content, Category::class, 'json');
            $errors = $validator->validate($category);
            if (count($errors) > 0 ) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->json($category, Response::HTTP_CREATED, [], [
                'groups' => 'list'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ]);
        }
    }

    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'list'
        ]);
    }

    #[Route('/{id}/edit', name: 'category_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $entityManager,
                         Request $request,
                         SerializerInterface $serializer,
                         ValidatorInterface $validator, Category $category): Response
    {

        $content = $request->getContent();

        try {
            $category = $serializer->deserialize($content, Category::class, 'json', [
                AbstractNormalizer::OBJECT_TO_POPULATE => $category
            ]);

            $errors = $validator->validate($category);
            if (count($errors) > 0 ) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }
            $entityManager->flush();
            return $this->json($category, Response::HTTP_CREATED, [], [
                'groups' => 'list'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ]);
        }


    }

    #[Route('/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Category $category): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->json($categoryRepository->findAll(), Response::HTTP_OK, [], [
            'groups' => 'list'
        ]);
    }
}
