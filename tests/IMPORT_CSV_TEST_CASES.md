# Cas de test import CSV

## 1) Fichier propre
- Fichier: `tests/fixtures/import_propre.csv`
- Mapping conseillé:
  - `first_name` -> `prenom`
  - `last_name` -> `nom`
  - `professional_email` -> `email`
  - `professional_phone` -> `telephone`
  - `business_name` -> `entreprise`
  - `city` -> `ville`
  - `country` -> `pays`
- Résultat attendu:
  - 2 succès
  - 0 erreur

## 2) Fichier cassé
- Fichier: `tests/fixtures/import_casse.csv`
- Même mapping que ci-dessus.
- Résultat attendu:
  - 0 succès
  - 2 erreurs
  - Messages lisibles avec numéro de ligne (`Ligne 2 invalide...`, `Ligne 3 invalide...`)
