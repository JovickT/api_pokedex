
---

### README.md (Proposition)

````md
#  Weather Advantage API (Symfony + Redis)

Ce projet est une API développée en **Symfony** permettant de déterminer si un **Pokémon a un avantage** en fonction de la **météo actuelle d’une ville**.  
L’API se connecte à un service météo externe (ex: OpenWeatherMap) et utilise un système de **cache Redis** pour optimiser les performances.

---

## Fonctionnalité principale

### Endpoint disponible

```http
GET /pokemon/{pokemon}/city/{city}
````

### Exemple :

```
https://127.0.0.1:8000/pokemon/bulbizarre/city/paris
```

### Réponse JSON (exemple) :

```json
{
  "ville": "Paris",
  "temps": "Clouds",
  "temperature": 15,
  "pokemon": {
    "id": 1,
    "name": "Bulbizarre",
    "type": "Plante",
    "state": true
  },
  "avantage": "Tié patraque bebeeew"
}
```

### Conditions d’avantage (exemple logique utilisée) :

| Type Pokémon | Condition météo               | Avantage   |
| ------------ | ----------------------------- | ---------- |
| Feu        | Pluie (Rain)                  | Aucun    |
| Eau        | Grand soleil (Clear) et ≥ 30° | Aucun    |
| Plante     | Nuageux (Clouds)              | Avantage |

---

## Technologies utilisées

| Technologie         | Utilisation               |
| ------------------- | ------------------------- |
| Symfony (v7.x)      | Framework PHP             |
| Doctrine ORM        | Base de données (Pokémon) |
| OpenWeatherMap API  | Récupération météo        |
| Redis               | Cache des réponses        |
| HTTP Client Symfony | Requête API météo         |
| PHP 8.2             | Langage du projet         |

---

## Configuration du cache Redis

### `.env`

```env
CACHE_POOL_DSN=redis://127.0.0.1:6379
REDIS_URL=redis://127.0.0.1:6379
API_KEY = {A renseigner}
```

### `config/packages/cache.yaml`

```yaml
framework:
  cache:
    app: cache.adapter.redis
    default_redis_provider: '%env(REDIS_URL)%'
```

---

## Installation du projet

```bash
git clone <url-du-projet>
cd my_project_directory
composer install
```

---

## Lancement du serveur Symfony

```bash
symfony server:start -d
```

> Accès à l’API via: `https://127.0.0.1:8000`

---

## Lancement du serveur Redis (si installé localement)

### Sous Windows (ex si Redis installé dans `C:\Redis`)

```bash
redis-server
```

### --> Vérifier qu’il fonctionne :

```bash
redis-cli ping
```

Doit renvoyer : `PONG`

---

## Monitoring des accès Redis

```bash
redis-cli monitor
```

Puis exécuter une requête à l’API. Vous verrez une commande du type :

```
nom de la requête :  "weather_Bulbizarre_Paris" ...
```

---

## Fonctionnement du cache

Lorsqu’un utilisateur appelle une ville + un pokémon :
Si la réponse **n'existe pas dans Redis**, elle est générée et stockée
Si elle existe déjà, elle est **servie directement depuis le cache (rapide)**

Pas encore fini
```

---

## Base de données Pokémon

L’entité Pokémon contient (exemple) :

| Champ | Type   |
| ----- | ------ |
| id    | entier |
| name  | string |
| type  | string |
| state | bool   |

---



---
```
