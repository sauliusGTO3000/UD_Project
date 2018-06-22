<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/browseImages", name="browseImages")
     */
    public function browseImages(ImageRepository $imageRepository){
        $listofimages = $imageRepository->findAll();
        $imageArray = [];

        foreach ($listofimages as $image){
            $imageArray[]=["image"=>"/uploads/images/".$image->getFilename()];
        }

        return new JsonResponse($imageArray);

    }
    /**
     * @Route("/", name="post_index", methods="GET")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', ['posts' => $postRepository->findAll()]);
    }

    /**
     * @Route("/new", name="post_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_show", methods="GET")
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods="GET|POST")
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_delete", methods="DELETE")
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }


    /**
     * @Route("/images", name="uploadImage")
     */
    public function uploadImage(Request $request, LoggerInterface $logger, ImageRepository $imageRepository){
        $file=$request->files->get('upload');
        /** @var UploadedFile $file */
        $filename = $this->generateUniqueFileName().'.'.$file->guessClientExtension();

        $file->move(
            //moves the image to pre-determined uploaded_images_directory in config/services.yaml
            $this->getParameter("uploaded_images_directory"),
            $filename
        );

//        add filename to database
        $em = $this->getDoctrine()->getManager();
        $imageInDB = new Image();
        $imageInDB->setFilename($filename);
        $em->persist($imageInDB);
        $em->flush();

        $image_url = '/uploads/images/'.$filename;

        return new JsonResponse(array(
            'uploaded'=>true,
            'url'=>$image_url,
        ));
    }


    private function generateUniqueFileName(){

        return md5(uniqid());
    }
}
