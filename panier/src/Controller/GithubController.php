<?php

namespace App\Controller;


use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\Request;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    /**
     * @Route("/connect/github", name="github")
     */
    public function index(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('github_main')->redirect([
            'connect_github_check' => 'email'
        ]);

    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/github/check", name="connect_github_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {

        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\GithubClient $client */
        $client = $clientRegistry->getClient('github_main');

        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\Github $user */

            $user1 = $client->fetchUser();

            $usersData = $user1->toArray();

            dump($usersData);
           die;
            return $this->redirectToRoute('home', array("email" => $usersData['email']));
        } catch (IdentityProviderException $e) {
            return $this->render('errors/index.html.twig');
        }

    }
}
