<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestWathatappsAPIController extends AbstractController
{
    #[Route('/test/api', name: 'test_api', methods: ['GET'])]
    public function test(string $instanceId, string $tokenId): JsonResponse
    {

        $client = new \UltraMsg\WhatsAppApi($tokenId,$instanceId);

        $to="33609284197";
        $body="comment vaty wesh";
        $api=$client->sendChatMessage($to,$body);
        dd($api);

        return new Response($content);
    }
}
