<?php

namespace App\Controller;

use App\Entity\Topics;
use App\Form\AddTopicType;
use App\Repository\TopicsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TopicController extends AbstractController
{
    #[Route('/topics', name: 'app_topic')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $topics = $doctrine->getRepository(Topics::class)->findAll();
        $form = $this->createForm(AddTopicType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($topic);
            $entityManager->flush();
            return $this->redirectToRoute('app_topic');
        }

        return $this->render('topic/index.html.twig', [
            'controller_name' => 'TopicController',
            'items' => $topics,
            'form' => $form->createView()
        ]);
    }

    #[Route('/topics/delete/{id}', name: 'topic_delete')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $topic = $doctrine->getRepository(Topics::class)->find($id);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($topic);
        $entityManager->flush();

        return $this->redirectToRoute('app_topic');
    }
}
