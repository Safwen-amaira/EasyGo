<?php

namespace App\Controller;

use App\Entity\TypeRecompense;
use App\Form\TypeRecompenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/type-recompense', name: 'type_recompense_')]
class TypeRecompenseController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        $types = $em->getRepository(TypeRecompense::class)->findAll();

        return $this->render('type_recompense/index.html.twig', [
            'types' => $types,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $type = new TypeRecompense();
        $form = $this->createForm(TypeRecompenseType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($type);
            $em->flush();

            return $this->redirectToRoute('type_recompense_index');
        }

        return $this->render('type_recompense/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypeRecompenseType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('type_recompense_index');
        }

        return $this->render('type_recompense/edit.html.twig', [
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $em->remove($type);
        $em->flush();

        return $this->redirectToRoute('type_recompense_index');
    }

    #[Route('/toggle/{id}', name: 'toggle')]
    public function toggle(TypeRecompense $type, EntityManagerInterface $em): Response
    {
        $type->setActif(!$type->isActif());
        $em->flush();

        return $this->redirectToRoute('type_recompense_index');
    }
}
