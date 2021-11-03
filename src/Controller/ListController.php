<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parent route set to /list
 */
#[Route('/list', name: "list.")]

class ListController extends AbstractController
{
    #[Route('/', name: 'showAll')]
    public function index(): Response
    {
        $tasks = $this->getDoctrine()->getRepository(Task::class)->findAll();
        return $this->render('list/index.html.twig', [
            'tasks' => $tasks,
        ]);
        
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, $id) 
    {
        $existingTask = $this->getDoctrine()->getRepository(Task::class)->findOneBy([ 'id' => $id]);
        $form = $this->createForm(TaskType::class, $existingTask);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($existingTask);
            $em->flush();
            
            return $this->redirect($this->generateUrl('list.showAll'));
        }

        return $this->render('list/edit-existing.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete() 
    {

    }
    #[Route('/new-task', name: 'new-task')]
    public function addNew(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $task->setDone(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            
            return $this->redirect($this->generateUrl('list.showAll'));
        }

        return $this->render('list/task-edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
