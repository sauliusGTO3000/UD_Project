<?php
/**
 * Created by PhpStorm.
 * User: SauliusGTO3000
 * Date: 7/29/2018
 * Time: 03:54
 */
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
 * @Route("/kitidalykai")
 */
class Kitidalykai extends Controller
{
    /**
     * @Route("/", name="kitidalykai", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('kitidalykai/index.html.twig');
    }

    /**
     * @Route("/allPostsForSuperAuthor", name="allPostsForSuperAuthor", methods="GET")
     */
    public function findAllPostedForSuperuser(PostRepository $postRepository){
        return $this->render('autoriausKampelis/allposts.html.twig');
    }

    /**
     * @Route("/privacy", name="privacy", methods="GET")
     */
    public function showprivacypolicy(){
        return $this->render('kitidalykai/privacypolicy.html.twig');
    }

}