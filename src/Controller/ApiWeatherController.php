<?php

namespace App\Controller; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiWeatherController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/meteo/{city}', name: 'api_meteo', methods: ['GET'])]
    public function wheatherApi(string $city): JsonResponse
    {
        $key = $_ENV['API_KEY'];

        $response = $this->client->request(
            'GET',
            "http://api.openweathermap.org/data/2.5/weather?q={$city}&appid={$key}&units=metric"
        );

        $data = $response->toArray(false);

        $cityName = $data["name"];
        $weather = $data["weather"];

        return new JsonResponse($data, 200);
    }
}
