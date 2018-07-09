<?php

namespace App\Controller;

use App\Entity\Hashtag;
use App\Entity\Image;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\HashtagRepository;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use App\Service\ImageResizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
//            $imageURL= $_SERVER["HTTP_HOST"]."/uploads/images/".$image->getFilename();
            $imageURL= $_SERVER["DOCUMENT_ROOT"].'\uploads\images\\'.$image->getFilename();
//            echo $imageURL;
            if (file_exists($imageURL)){
                $imageArray[]=["image"=>"/uploads/images/".$image->getFilename()];
            }

        }
        return new JsonResponse($imageArray);

    }
    /**
     * @Route("/", name="post_index", methods="GET")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', ['posts' => $postRepository->findPosted()]);
    }

    /**
     * @Route("/new", name="post_new", methods="GET|POST")
     */
    public function new(Request $request, UserInterface $author): Response
    {
        $post = new Post();

        $post->setDateCreated(new \DateTime());
        $post->setAuthor($author);
        $post->setReadCount(0);
        $post->setShortContent($this->generateShortContent($post->getContent()));
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
    public function show(Post $post, HashtagRepository $hashtagRepository): Response
    {   $readCount = $post->getReadCount();
        $readCount++;
        $post->setReadCount($readCount);

        if ($post->getHashtags() !== null){
            foreach ($post->getHashtags() as $hashtagitem){
                $hashtagindb = $hashtagRepository->find($hashtagitem);
                $hashtagReadCountInDb = $hashtagindb->getReadCount();
                $hashtagReadCountInDb++;
                $hashtagindb->setReadCount($hashtagReadCountInDb);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

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
            $post->setShortContent($this->generateShortContent($post->getContent()));
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
     * @Route("/uploadCoverImage", name="uploadCoverImage")
     */
    public function uploadCoverImage(){

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

//        $this->resizeImage('C:\xampp\htdocs\namu_darbai\Liepos3\UD_Project\my_project\public\uploads\images\\'.$filename);
        $this->resizeImage($_SERVER["DOCUMENT_ROOT"].'\uploads\images\\'.$filename,700);

        return new JsonResponse(array(
            'uploaded'=>true,
            'url'=>$image_url,
        ));
    }


    private function generateUniqueFileName(){

        return md5(uniqid());
    }

    private function generateShortContent($content, $wordsToKeep=200)
    {
        $length = mb_strlen($content);

        $arrayOfTags = [];
        $tag="";
        $stringWithClosingTags="";
        $wordCount=0;
        $wordCutMarker=0;

        for ($i=0; $i < $length; $i++) {
            if ($content[$i]=="<") {

                while ($content[$i]!=">") {
                    $tag .= ($content[$i]);
                    $i++;
                }
            }
            if ($content[$i]==">") {
                $tag .= (">");
                $arrayOfTags[]=$tag;
                $tag="";

            }
            if ($content[$i]==" " or $content[$i]=="&") {
                $wordCount++;
                if ($wordCount<=$wordsToKeep) {
                    $wordCutMarker = $i;
                }
            }
        }

        for ($i=0; $i < count($arrayOfTags); $i++) {
            $tagToBeChecked = $arrayOfTags[$i];

            if ($tagToBeChecked[1]=="/") {
                unset($arrayOfTags[$i]);
                unset($arrayOfTags[$i-1]);
                $arrayOfTags = array_values($arrayOfTags);
                $i = $i-2;
            }

            if ($tagToBeChecked[1]=="a") {
                unset($arrayOfTags[$i]);
                $arrayOfTags = array_values($arrayOfTags);
            }

            if ($tagToBeChecked[(strlen($tagToBeChecked)-2)]=="/") {
                unset($arrayOfTags[$i]);
                $arrayOfTags = array_values($arrayOfTags);
                $i = $i-1;
            }

        }

        foreach ($arrayOfTags as $key => $value) {
            $stringWithClosingTags.=substr_replace($value, "/", 1, 0);
        }
        $content = substr($content, 0, $wordCutMarker).$stringWithClosingTags;
        return $content;
    }

    public function resizeImage($imageURL, $maxWidth){

        $resizedIMG = new ImageResizer($imageURL);
        $imageWidth = $resizedIMG->getWidth();

        if ($imageWidth<$maxWidth){
            $resizedIMG->saveImage($imageURL);
        }else{
            $resizedIMG->resizeImage($maxWidth, 0, 'landscape');
            $resizedIMG->saveImage($imageURL);
        }
        return;
    }
}
