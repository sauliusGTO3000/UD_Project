<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorFrontType;
use App\Form\AuthorPasswordType;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Repository\PostRepository;
use App\Service\ImageResizer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/author")
 */
class AuthorController extends Controller
{
    /**
     * @Route("/", name="author_index", methods="GET")
     */
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('author/index.html.twig', ['authors' => $authorRepository->findAll()]);
    }

    /**
     * @Route("/editauthorpassword", name="editauthorpassword", methods="GET|POST")
     * @param Request $request
     * @param UserInterface $author
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAuthorPassword(Request $request, UserInterface $author, UserPasswordEncoderInterface $passwordEncoder){
        $form = $this->createForm(AuthorPasswordType::class, $author);

//        var_dump($author->getPassword());

        $form->handleRequest($request);
//        var_dump($author->getPassword());



        if ($form->isSubmitted() && $form->isValid())
        {
            if($passwordEncoder->isPasswordValid($author,$author->getOldPassword()))
            {
            $password = $passwordEncoder->encodePassword($author,$author->getPlainPassword());
            $author->setPassword($password);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('homepage');
            }
            echo "please enter correct old password";

        }

        return $this->render('author/editauthorpassword.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/editauthordetails", name="editauthordetails", methods="GET|POST")
     * @param Request $request
     * @param Author $author
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAuthorFront(Request $request, UserInterface $author){
        $form = $this->createForm(AuthorFrontType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($author->getProfilePictureFile() != null){
                $file = $author->getProfilePictureFile();
                $filename = $author->getId().'.'.$file->guessClientExtension();;
                $file->move(
                //moves the image to pre-determined uploaded_images_directory in config/services.yaml
                    $this->getParameter("uploaded_author_images_directory"),
                    $filename
                );
                $image_url = '/uploads/authorprofileimages/'.$filename;
                $author->setProfilePicture($image_url);
                $author->setProfilePictureFile($image_url);
                $this->resizeAuthorImage($this->getParameter("uploaded_author_images_directory")."/".$filename,200);
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('author/editauthordetails.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/new", name="author_new", methods="GET|POST")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($author,$author->getPassword());
            $author->setPassword($password);
            if ($author->getProfilePictureFile() != null){
                $file = $author->getProfilePictureFile();
                $filename = $author->getId().'.'.$file->guessClientExtension();;
                $file->move(
                //moves the image to pre-determined uploaded_images_directory in config/services.yaml
                    $this->getParameter("uploaded_author_images_directory"),
                    $filename
                );
                $image_url = '/uploads/authorprofileimages/'.$filename;
                $author->setProfilePicture($image_url);
                $author->setProfilePictureFile($image_url);
                $this->resizeAuthorImage($this->getParameter("uploaded_author_images_directory")."/".$filename,200);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list", name="author_list", methods="GET")
     */
    public function showAllAuthors(AuthorRepository $authorRepository){
        return $this->render('author/list.html.twig', ['authors' => $authorRepository->findAll()]);

    }


    /**
     * @Route("/{id}", name="author_show", methods="GET")
     */
    public function show(Author $author, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findByAuthor($author);
        return $this->render('author/show.html.twig', [
            'author' => $author,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/{id}/edit", name="author_edit", methods="GET|POST")
     */
    public function edit(Request $request, Author $author, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($author,$author->getPassword());
            $author->setPassword($password);
            if($author->getProfilePictureFile()!= null){
                $this->uploadAuthorImage($author);
            }


            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('author_edit', ['id' => $author->getId()]);
        }

        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="author_delete", methods="DELETE")
     */
    public function delete(Request $request, Author $author): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($author);
            $em->flush();
        }

        return $this->redirectToRoute('author_index');
    }

    public function uploadAuthorImage(Author $author){
        $file = $author->getProfilePictureFile();
        $filename = $author->getId().'.'.$file->guessClientExtension();;

        $file->move(
        //moves the image to pre-determined uploaded_images_directory in config/services.yaml
            $this->getParameter("uploaded_author_images_directory"),
            $filename
        );
        $image_url = '/uploads/authorprofileimages/'.$filename;

        $author->setProfilePicture($image_url);
        $this->resizeAuthorImage($this->getParameter("uploaded_author_images_directory")."/".$filename,200);

    }

    public function resizeAuthorImage($imageURL, $maxWidth){

        $resizedIMG = new ImageResizer($imageURL);
        $imageWidth = $resizedIMG->getWidth();

        if ($imageWidth<$maxWidth){
            $resizedIMG->saveImage($imageURL);
            $resizedIMG->resizeImage($maxWidth, 200, 'crop');
        }else{
            $resizedIMG->resizeImage($maxWidth, 200, 'crop');
            $resizedIMG->greyScale();
            $resizedIMG->saveImage($imageURL);
        }
        return;
    }


}
