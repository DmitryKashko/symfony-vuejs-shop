<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\EditProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}


    #[Route('/', name: 'main_homepage', methods: "GET|POS")]
    public function index(Request $request): Response
    {
        $productList = $this->entityManager->getRepository(Product::class)->findAll();

        return $this->render('main/default/index.html.twig');
    }

    #[Route('/edit-product/{id}', name: 'product_edit', requirements: ['id' => "\d+"], methods: "GET|POST")]
    #[Route('/add-product', name: 'product_add', methods: "GET|POST")]
    public function editProduct(Request $request, int $id  = null): Response
    {
        if($id) {
            $product = $this->entityManager->getRepository(Product::class)->find($id);
        } else {
            $product = new Product();
        }
        $form = $this->createForm(EditProductFormType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('main/default/edit_product.html.twig', [
            'form' => $form->createView()
            ]);

    }
}
