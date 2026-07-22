<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tache')]
final class TacheController extends AbstractController
{
    #[Route(name: 'app_tache_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findAllOrderByPriorite(),
        ]);
    }

    #[Route('/mes-taches', name: 'app_tache_mes_taches', methods: ['GET'])]
    public function mesTaches(TacheRepository $tacheRepository): Response
    {
        $user = $this->getUser();

        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findByAssigneA($user),
        ]);
    }

    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $tache = new Tache();
    $tache->setDataCreation(new \DateTimeImmutable());

    $form = $this->createForm(TacheType::class, $tache);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($tache);
        $entityManager->flush();

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('tache/new.html.twig', [
        'tache' => $tache,
        'form' => $form,
    ]);
}

    // IMPORTANT : ces routes doivent être déclarées AVANT '/{id}' (show)
    #[Route('/{id}/statut/{nouveauStatut}', name: 'app_tache_changer_statut', methods: ['GET'])]
    public function changerStatut(Tache $tache, string $nouveauStatut, EntityManagerInterface $entityManager): Response
    {
        $statutsValides = ['a_faire', 'en_cours', 'terminee'];

        if (in_array($nouveauStatut, $statutsValides, true)) {
            $tache->setStatut($nouveauStatut);
            $entityManager->flush();
            $this->addFlash('success', 'Statut mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Statut invalide.');
        }

        return $this->redirectToRoute('app_tache_index');
    }

    #[Route('/{id}/assigner-moi', name: 'app_tache_assigner_moi', methods: ['GET'])]
    public function assignerMoi(Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $tache->setAssigneA($this->getUser());
        $entityManager->flush();

        $this->addFlash('success', 'Tâche assignée avec succès.');
        return $this->redirectToRoute('app_tache_index');
    }

    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}