<?php
/**
 * Created by PhpStorm.
 * User: SauliusGTO3000
 * Date: 7/20/2018
 * Time: 01:47
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Controller\LoginController;

class KitiDalykai extends Controller
{
    /**
     * @Route("/kitidalykai", name="kitidalykai")
     */
    public function renderHomepage(LoginController $loginController, Request $request, AuthenticationUtils $authenticationUtils){
        echo "hi";
        return $loginController->login($request, $authenticationUtils);

    }
}