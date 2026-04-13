# Intégrer `compass/crawler-google-places` via API HTTP

## 1) Définir le token API

```bash
API_TOKEN=<YOUR_API_TOKEN>
```

## 2) Préparer l'input Actor

```bash
cat > input.json << 'JSON'
{
  "searchStringsArray": [
    "restaurant"
  ],
  "locationQuery": "New York, USA",
  "maxCrawledPlacesPerSearch": 50,
  "language": "en",
  "scrapeSocialMediaProfiles": {
    "facebooks": false,
    "instagrams": false,
    "youtubes": false,
    "tiktoks": false,
    "twitters": false
  },
  "maximumLeadsEnrichmentRecords": 0
}
JSON
```

## 3) Lancer l'Actor

```bash
curl "https://api.apify.com/v2/acts/compass~crawler-google-places/runs?token=$API_TOKEN" \
  -X POST \
  -d @input.json \
  -H 'Content-Type: application/json'
```

Référence API: <https://docs.apify.com/api/v2>
