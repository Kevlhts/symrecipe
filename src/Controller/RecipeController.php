<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecipeController extends AbstractController
{
    /**
     * Controller : display all recipes
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $repository,
        PaginatorInterface $paginator,
        Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes'=> $recipes,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', 'recipe.new',methods: ['GET','POST'])]
    public function new(Request $request,EntityManagerInterface $manager) :Response
    {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest(($request));
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été crééee avec succès'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
                'form' => $form->createView()
            ]
        );
    }


    /**
     * Controller : Edit recipe
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
        #[Route('/recette/edition/{id}/', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
            Recipe                 $recipe,
            Request                $request,
            EntityManagerInterface $manager
        ): Response
        {
            $form = $this->createForm(RecipeType::class, $recipe);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $recipe = $form->getData();

                $manager->persist($recipe);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre recette a été modifié avec succès ! '
                );

                return $this->redirectToRoute('recipe.index');
            }
            return $this->render('pages/recipe/edit.html.twig', [
                'form' => $form->createView()
            ]);
        }

    /**`
     * Delete recipe
     * @param Recipe $recipe
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
        #[Route('/recipe/suppression/{id}/','recipe.delete', methods: ['GET'])]
        public function delete(
            Recipe $recipe,
            EntityManagerInterface $entityManager,
        ) : Response {

            $entityManager->remove($recipe);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été supprimé avec succès ! '
            );

            return $this->redirectToRoute('recipe.index');
        }
}
