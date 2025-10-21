<?php
// src/Controller/PokemonController.php
namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class PokemonController extends AbstractController
{
    #[Route('/add', name: 'create_pokemon', methods: ['POST'])]
    public function create(Request $request, PokemonRepository $pokemonRepository): JsonResponse
    {
        // Récupérer les données envoyées 
        $data = json_decode($request->getContent(), true);


        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $name = $data['name'] ?? null;
        $type = $data['type'] ?? null;

        // Vérification
        if (!$name || !$type) {
            return new JsonResponse(['error' => 'Name and type are required'], 400);
        }

        // Création
        $pokemon = $pokemonRepository->create($name, $type);

        return new JsonResponse([
            'message' => 'Pokémon créé avec succès',
            'pokemon' => [
                'id' => $pokemon->getId(),
                'name' => $pokemon->getName(),
                'type' => $pokemon->getType(),
                // 'state' => $pokemon->isState(),
            ]
        ], 201);
    }
    #[Route('/read', name: 'read_pokemon', methods: ['GET'])]
    public function read(PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemons = $pokemonRepository->findAll();

        $data = [];
        foreach ($pokemons as $pokemon) {
            $data[] = [
                'id' => $pokemon->getId(),
                'name' => $pokemon->getName(),
                'type' => $pokemon->getType(),
                'state' => $pokemon->isState(),
            ];
        }

        return new JsonResponse($data, 200);
    }

    #[Route('/patch/{id}', name: 'patch_pokemon', methods: ['PATCH'])]
    public function patch(int $id, Request $request, PokemonRepository $pokemonRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            return new JsonResponse(['error' => 'Pokemon not found'], 404);
        }

        if (isset($data['name'])) {
            $pokemon->setName($data['name']);
        }

        if (isset($data['type'])) {
            $pokemon->setType($data['type']);
        }

        $pokemonRepository->save($pokemon);

        return new JsonResponse([
            'message' => 'Pokémon mis à jour avec succès',
            'pokemon' => [
                'id' => $pokemon->getId(),
                'name' => $pokemon->getName(),
                'type' => $pokemon->getType(),
            ]
        ], 200);
    }

    #[Route('/delete/{id}', name: 'delete_pokemon', methods: ['DELETE'])]
    public function delete(int $id, PokemonRepository $pokemonRepository): JsonResponse
    {

        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            return new JsonResponse(['error' => 'Pokemon not found'], 404);
        }

        $pokemonRepository->remove($pokemon);

        return new JsonResponse([
            'message' => 'Pokémon supprimer avec succès',
        ], 200);
    }

    #[Route('/pokemon/{pokemon}/city/{city}', name: 'city_pokemon', methods: ['GET'])]
    public function wiknessornot(string $pokemon, string $city, Request $request, PokemonRepository $pokemonRepository, ApiWeatherController $apiWeatherController, CacheInterface $cache): JsonResponse
    {


        if (!$city || !$pokemon) {
            return new JsonResponse(['error' => 'misses informations in url'], 400);
        }

       $cacheKey = "weather_{$pokemon}_{$city}";

        $infos = $cache->get($cacheKey, function(ItemInterface $item) use (
            $pokemon, $city, $pokemonRepository, $apiWeatherController
        ) {
            $item->expiresAfter(3600);
            
            $pokemon = ucfirst(strtolower($pokemon));
            $find = $pokemonRepository->findOneBy(['name' => $pokemon]);

            if (!$find) {
                throw new \Exception('Pokemon not found');
            }

            $pokemonData = [
                'id' => $find->getId(),
                'name' => $find->getName(),
                'type' => $find->getType(),
                'state' => $find->isState(),
            ];

            $apiResponse = $apiWeatherController->wheatherApi($city);
            $data = json_decode($apiResponse->getContent(), true);
            $weather = $data["weather"][0]["main"];
            $temp = $data["main"]["temp"];

            $infos = [
                "ville" => $data["name"],
                "temps" => $weather,
                "temperature" => $temp,
                "pokemon" => $pokemonData,
                "avantage" => "Tié un monstre"
            ];

            if((($infos["pokemon"]["type"] == "Feu") && ($infos["temps"] == "Rain")) 
            || (($infos["pokemon"]["type"] == "Eau") && ($infos["temps"] == "Clear") && ($infos["temperature"] >= 30)) 
            || (($infos["pokemon"]["type"] == "Plante") && ($infos["temps"] == "Clouds"))) {
                $infos["avantage"] = "Tié patraque bebeeew";
            }

            return $infos;
        });

        return new JsonResponse($infos, 200);
    }
}
