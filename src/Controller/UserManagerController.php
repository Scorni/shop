<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user/manager')]
class UserManagerController extends AbstractController
{
    #[Route('/', name: 'app_user_manager_index', methods: ['GET'])]
    public function index(UserRepository $userRepository,AuthenticationUtils $authenticationUtils,EntityManagerInterface $entityManager): Response
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['lastname' => $authenticationUtils->getLastUsername()]);
        if($user->getRole()->getId() === 1){
            return $this->render('user_manager/index.html.twig', [
                'users' => $userRepository->findAll(),
            ]);
        }else{
            return $this->redirectToRoute('app_error_eligi',[], Response::HTTP_SEE_OTHER);

        }

    }

    #[Route('/new', name: 'app_user_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_manager/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_manager_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_manager/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_manager/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_manager_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_manager_index', [], Response::HTTP_SEE_OTHER);
    }
}
