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
use App\Service\ArchiveBuilder;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/browseimages", name="browseimages")
     */
    public function browseimages(ImageRepository $imageRepository){
        $listofimages = $imageRepository->findAll();
        $imageArray = [];
        foreach ($listofimages as $image){
            $imageURL= $_SERVER["DOCUMENT_ROOT"].'/uploads/images/'.$image->getFilename();
            if (file_exists($imageURL)){
                $imageArray[]=["image"=>"/uploads/images/".$image->getFilename()];
            }
        }
        return new JsonResponse($imageArray);
    }



    /**
     * @Route("/coverimagebrowser", name="coverimageBrowser", methods="GET|POST")
     */
    public function coverImageBrowser(){
        return $this->render('post/coverImageBrowser.html.twig');
    }

    /**
     * @Route("/archive", name="archive", methods="GET")
     */
    public function generateArchive(ArchiveBuilder $archiveBuilder){

        return $this->render('post/archive.html.twig', ['posts' => $archiveBuilder->getArchiveData()]);
    }

    /**
     * @Route("/archivesmall", name="archivesmall", methods="GET")
     */
    public function generateSmallArchive(ArchiveBuilder $archiveBuilder){

        return $this->render('post/archive.html.twig', ['posts' => $archiveBuilder->getArchiveData(10)]);
    }

    /**
     * @Route("/topfive", name="topfive", methods="GET")
     */
    public function findTopFive(PostRepository $postRepository){

        return $this->render('post/topfive.html.twig', ['posts' => $postRepository->findTopFive()]);
    }

    /**
     * @Route("/", name="post_index", methods="GET")
     */
    public function index(PostRepository $postRepository): Response
    {

        return $this->render('post/homepage.html.twig');
    }



    /**
     * @Route("/infiniteScrollJSON", name="infiniteScrollJSON", methods="GET")
     */
    public function postsNow(Request $request, PostRepository $postRepository){

//            $em    = $this->get('doctrine.orm.entity_manager');
//            $dql   = "SELECT p FROM App\Entity\Post p WHERE p.publishedDate < NOW";
            $query = $postRepository->findPosted();
            $items = [];


            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );
//            var_dump($maxLoadCount);

            /** @var Post  $post */
        foreach ($pagination as $post){
                $items[]=array(
                    'id' => $post->getId(),
                    'coverImage'=> $post->getCoverImage(),
                    'title' => $post->getTitle(),
                    'shortContent' => $post->getShortContent(),
                    'publishedDate' => $post->getPublishDate(),
                );

            }
        $maxLoadCount = ceil(count($query)/10);
            // parameters to template
            return new JsonResponse(['pages' => $items,'maxLoadCount' => $maxLoadCount]);

    }


    /**
     * @Route("/homepage", name="homepage", methods="GET")
     */
    public function Homepage(){
        return $this->render('post/homepage.html.twig');
    }

    /**
     * @Route("/false", name="false", methods="GET|POST")
     */
    public function nothing(){
        return $this->render('post/nothing.html.twig');
    }

    /**
     * @Route("/new", name="post_new", methods="GET|POST")
     */
    public function new(Request $request, UserInterface $author): Response
    {
        $post = new Post();


        $post->setDateCreated(new \DateTime());
        $post->setPublishDate(new \DateTime());
        $post->setAuthor($author);
        $post->setReadCount(0);


        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //cover image function starts here
            $this->addCoverImageToPost($post);
            //cover image function ends here
            $post->setShortContent($this->generateShortContent($post->getContent()));
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

            //cover image function starts here
            $this->addCoverImageToPost($post);
            //cover image function ends here

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
     * @Route("/images", name="uploadImage")
     */
    public function uploadImage(Request $request){
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
        $this->resizeImage($this->getParameter("uploaded_images_directory")."/".$filename,800);

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
        if ($length < $wordsToKeep){
            return $content;
        }
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
        $content = substr($content, 0, $wordCutMarker)."...".$stringWithClosingTags;
        return $content;
    }

    public function stretchCoverImage($imageURL, $stretchedWidth){
        $resizedIMG = new ImageResizer($imageURL);
        $imageWidth = $resizedIMG->getWidth();

            $resizedIMG->resizeImage($stretchedWidth, 400, 'crop');
            $resizedIMG->saveImage($imageURL);

        return;
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



    public function addCoverImageToPost($post){
        if ($post->getCoverImageFile() !== null)
        {
            $coverImageFile = $post->getCoverImageFile();
            $coverImageFileName = $this->generateUniqueFileName().'.'. $coverImageFile->guessClientExtension();
            $coverImageFile->move(
                $this->getParameter("uploaded_images_directory"),
                $coverImageFileName

            );

            $post->setCoverImage("/uploads/images/".$coverImageFileName);

            $this->addCoverImageToDB($coverImageFileName);
            $this->stretchCoverImage($this->getParameter("uploaded_images_directory")."/".$coverImageFileName,800);
        }
        if ($post->getCoverImage() == null)
        {
            $stockImageNumber=rand(1,26);
            $post->setCoverImage("/uploads/images/".$stockImageNumber.".png");
        }

    }

    public function addCoverImageToDB($coverImageFileName){

        $coverImageInDB = new Image();
        $coverImageInDB->setFilename($coverImageFileName);
        $em = $this->getDoctrine()->getManager();
        $em->persist($coverImageInDB);
        $em->flush();

        $this->resizeImage($this->getParameter("uploaded_images_directory")."/".$coverImageFileName,800);
    }








}
