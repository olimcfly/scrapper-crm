# PHP CRM Backend Setup (O2Switch-friendly)

## 1) Fichiers de configuration
1. Copier `.env.example` en `.env` (ou configurer les variables dans le panel O2Switch).
2. Renseigner `DB_PASSWORD`.

## 2) Base de données
- Importer `database/schema.sql` dans MySQL.

## 3) Point d'entrée
- Le point d'entrée API est `public/index.php`.
- Avec Apache, activer `mod_rewrite` (fichier `public/.htaccess` inclus).

## 4) Endpoints clés
- `GET /health`
- `GET /prospects`
- `GET /prospects/{id}`
- `POST /prospects`
- `PUT /prospects/{id}`
- `DELETE /prospects/{id}`
- `POST /prospects/{id}/notes`
- `PATCH /prospects/{id}/status`
- `GET /prospect-statuses`
- `GET /sources`
- `GET /tags`
