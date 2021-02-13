<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ProductController.
 */
#[Route('/api/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'product_list', methods: ['GET'])]
    public function list(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll(), Response::HTTP_OK, [], [
            'groups' => 'list'
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['POST'])]
    public function new(
        EntityManagerInterface $entityManager,
        Request $request,
    SerializerInterface $serializer,
    ValidatorInterface $validator): Response
    {

        $content = $request->getContent();

        try {
            $product = $serializer->deserialize($content, Product::class, 'json');
            $errors = $validator->validate($product);
            if (count($errors) > 0 ) {
                return $this->json($errors, Response::HTTP_BAD_REQUEST);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->json($product, Response::HTTP_CREATED, [], [
                'groups' => 'list'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => $e->getMessage()
            ]);
        }
    }

    #[Route('/{id}', name: 'product', methods: ['GET'])]
    public function getProduct(Product $product): Response
    {
        return $this->json($product, Response::HTTP_OK, [], [
            'groups' => 'list'
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
