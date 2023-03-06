<?php

namespace App\Controller;

use App\Entity\Items;
use App\Entity\User;
use App\Form\ItemsType;
use App\Repository\ItemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/items')]
class ItemsController extends AbstractController
{
    private $user;

    #[Route('/', name: 'app_items_index', methods: ['GET'])]
    public function index(ItemsRepository $itemsRepository,SessionInterface $session): Response
    {
        $itemSession = $session->all();

        return $this->render('items/index.html.twig', [
            'items' => $itemsRepository->findAll(),
            'itemSession' =>$itemSession
        ]);
    }

    #[Route('/new', name: 'app_items_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ItemsRepository $itemsRepository,AuthenticationUtils $authenticationUtils,EntityManagerInterface $entityManager): Response
    {
        $this->user = $entityManager->getRepository(User::class)->findOneBy(['name' => $authenticationUtils->getLastUsername()]);

        $item = new Items();
        $form = $this->createForm(ItemsType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setUser($this->user);
            $itemsRepository->save($item, true);

            return $this->redirectToRoute('app_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('items/new.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_items_show', methods: ['GET'])]
    public function show(Items $item): Response
    {
        return $this->render('items/show.html.twig', [
            'item' => $item,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_items_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Items $item, ItemsRepository $itemsRepository): Response
    {
        $form = $this->createForm(ItemsType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemsRepository->save($item, true);

            return $this->redirectToRoute('app_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('items/edit.html.twig', [
            'item' => $item,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_items_delete', methods: ['POST'])]
    public function delete(Request $request, Items $item, ItemsRepository $itemsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
            $itemsRepository->remove($item, true);
        }

        return $this->redirectToRoute('app_items_index', [], Response::HTTP_SEE_OTHER);
    }


}
