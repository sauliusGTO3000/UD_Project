<?php

namespace App\Controller;

use App\Entity\Subscriber;
use App\Form\SubscriberType;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscriber")
 */
class SubscriberController extends Controller
{
    /**
     * @Route("/", name="subscriber_index", methods="GET")
     */
    public function index(SubscriberRepository $subscriberRepository): Response
    {
        return $this->render('subscriber/index.html.twig', ['subscribers' => $subscriberRepository->findAll()]);
    }

    /**
     * @Route("/new", name="subscriber_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();

            return $this->redirectToRoute('subscriber_index');
        }

        return $this->render('subscriber/new.html.twig', [
            'subscriber' => $subscriber,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subscriber_show", methods="GET")
     */
    public function show(Subscriber $subscriber): Response
    {
        return $this->render('subscriber/show.html.twig', ['subscriber' => $subscriber]);
    }

    /**
     * @Route("/{id}/edit", name="subscriber_edit", methods="GET|POST")
     */
    public function edit(Request $request, Subscriber $subscriber): Response
    {
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('subscriber_edit', ['id' => $subscriber->getId()]);
        }

        return $this->render('subscriber/edit.html.twig', [
            'subscriber' => $subscriber,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="subscriber_delete", methods="DELETE")
     */
    public function delete(Request $request, Subscriber $subscriber): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subscriber->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscriber);
            $em->flush();
        }

        return $this->redirectToRoute('subscriber_index');
    }
}
