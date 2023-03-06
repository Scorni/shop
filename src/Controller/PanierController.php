<?php

namespace App\Controller;

use App\Entity\Items;
use App\Repository\ItemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }
    #[Route('/{id}/addPanier', name: 'app_items_add_panier', methods: ['GET', 'POST'])]
    public function addToPanier(Request $request, Items $item, ItemsRepository $itemsRepository,SessionInterface $session): Response
    {

        $objectList = $session->get('object_list', []); // Retrieve the current object list from the session or an empty array if it does not exist
        $objectList[] = $item; // Add the item to the object list
        $session->set('object_list', $objectList); // Store the updated object list back in the session

        return $this->redirectToRoute('app_items_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/deleteInPanier', name: 'app_items_delete_in_panier', methods: ['GET', 'POST'])]
    public function deleteInPanier(Request $request, Items $item, ItemsRepository $itemsRepository,SessionInterface $session): Response
    {

        $objectList = $session->get('object_list', []); // Retrieve the current object list from the session or an empty array if it does not exist
        $index = array_search($item, $objectList); // Find the index of the item in the object list

        if ($index !== false) { // If the item exists in the object list
            unset($objectList[$index]); // Remove the item from the object list using the index
            $session->set('object_list', $objectList); // Save the updated object list back to the session
        }

        return $this->redirectToRoute('app_items_index', [], Response::HTTP_SEE_OTHER);
    }
}
