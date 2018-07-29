<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Form\HashtagType;
use App\Repository\HashtagRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hashtag")
 */
class HashtagController extends Controller
{
    /**
     * @Route("/", name="hashtag_index", methods="GET")
     */
    public function index(HashtagRepository $hashtagRepository): Response
    {
        return $this->render('hashtag/index.html.twig', ['hashtags' => $hashtagRepository->findAll()]);
    }

    /**
     * @Route("/showall", name="showallhashtags", methods="GET")
     */
    public function showAll(HashtagRepository $hashtagRepository){
        return $this->render('hashtag/showAll.html.twig', ['hashtag' => $hashtagRepository->findAllActiveHashtags()]);
    }

    /**
     * @Route("/showallBig", name="showallBig", methods="GET")
     */
    public function showAllBig(HashtagRepository $hashtagRepository){
        return $this->render('hashtag/hashtagsShowAllbig.html.twig', ['hashtag' => $hashtagRepository->findAllActiveHashtags()]);
    }

    /**
     * @Route("/new", name="hashtag_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $hashtag = new Hashtag();
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($hashtag);
            $em->flush();

            return $this->redirectToRoute('hashtag_index');
        }

        return $this->render('hashtag/new.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hashtag_show", methods="GET")
     */
    public function show(Hashtag $hashtag): Response
    {
        return $this->render('hashtag/show.html.twig', ['hashtag' => $hashtag]);
    }

    /**
     * @Route("/{id}/edit", name="hashtag_edit", methods="GET|POST")
     */
    public function edit(Request $request, Hashtag $hashtag): Response
    {
        $form = $this->createForm(HashtagType::class, $hashtag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hashtag_edit', ['id' => $hashtag->getId()]);
        }

        return $this->render('hashtag/edit.html.twig', [
            'hashtag' => $hashtag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hashtag_delete", methods="DELETE")
     */
    public function delete(Request $request, Hashtag $hashtag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hashtag->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($hashtag);
            $em->flush();
        }

        return $this->redirectToRoute('hashtag_index');
    }

    /**
     * @Route("/newhashtag/{name}", name="newhashtag", methods="GET|POST")
     */
    public function newHashtag($name){
        $hashtag = new Hashtag();
        $hashtag->setHashtagName($name);
        $em = $this->getDoctrine()->getManager();
        $em->persist($hashtag);
        $em->flush();
        return new JsonResponse([
            'id'=>$hashtag->getId(),
            'name'=>$hashtag->getHashtagName(),
        ]);
    }
    /**
     * @Route("/posts/{hashtagid}", name="postsByTitle", methods="GET")
     */
    public function showPostsByHashtag($hashtagid, HashtagRepository $hashtagRepository,PostRepository $postRepository){
//        $hashtagtobeshown = $hashtagRepository->find($hashtagid);

        return $this->render('post/byhashtag.html.twig', ['posts' => $postRepository->findByTags($hashtagid),'hashtag'=>$hashtagRepository->find($hashtagid)]);

    }




}
