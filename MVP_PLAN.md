# Transformation du cahier des charges en MVP concret (Full PHP + MySQL)

_Date : 12 avril 2026_

## A) Synthèse produit

Le produit visé est un **CRM de prospection simple, actionnable rapidement** et orienté exécution commerciale quotidienne.

### Objectif business V1
- Centraliser les prospects.
- Donner une vision claire du pipeline (statuts).
- Permettre un suivi concret (notes, historique, prochaines actions).
- Préparer des actions marketing simples (campagnes email basiques, import CSV).

### Positionnement V1
- **Outil d’opération commerciale léger**, pas une suite marketing complète.
- Priorité à la fiabilité et à la rapidité de prise en main (desktop + mobile lisible).
- Architecture simple MVC PHP + MySQL, maintenable sans dette framework lourde.

---

## B) MVP recommandé (périmètre indispensable V1)

## 1) Module “Prospects” (coeur)
- CRUD prospect (créer, lister, voir détail, modifier, archiver/supprimer soft selon contrainte).
- Champs minimaux : prénom, nom, email, téléphone, société, source, statut, tags, date de création.
- Recherche simple + filtres essentiels (statut, source, tag).

## 2) Module “Pipeline / Statuts”
- Statuts standards (ex: Nouveau, Contacté, Qualifié, Proposition, Gagné, Perdu).
- Changement de statut depuis fiche prospect.
- Vue pipeline simple (kanban minimal) ou fallback en liste groupée par statut.

## 3) Module “Notes & Historique”
- Ajout de notes horodatées sur chaque prospect.
- Historique automatique des événements clés : création, changement de statut, ajout de note.

## 4) Module “Import CSV fiable”
- Import de prospects avec mapping de colonnes basique.
- Validation stricte (email format, doublons email/téléphone si activé).
- Rapport d’import (lignes importées, rejetées, motifs).

## 5) Module “Authentification”
- Login/logout session PHP.
- Gestion minimale des rôles (admin unique ou user unique en V1 selon cible).

## 6) Dashboard minimal
- KPI vraiment utiles et dynamiques :
  - total prospects,
  - nouveaux sur 30 jours,
  - prospects par statut,
  - activités récentes.

---

## C) Modules à reporter plus tard (hors MVP)

- Automatisation email avancée (séquences, déclencheurs, scoring).
- Paramétrage complexe des champs personnalisés.
- Moteur de recherche globale transverse.
- Analytics avancées (cohortes, conversion multi-étapes détaillée, forecasting).
- Intégrations externes (LinkedIn, enrichissement tiers, webhooks entrants/sortants).
- Permissions multi-rôles fines et audit sécurité avancé.
- Centre de notifications sophistiqué.

---

## D) Roadmap de développement par phases

## Phase 0 — Fondations techniques (2-3 jours)
- Finaliser socle MVC PHP + MySQL + routing + vues.
- Standardiser validation serveur + gestion erreurs.
- Ajouter garde-fous sécurité de base (requêtes préparées, session sécurisée, CSRF).
- Mettre en place logs applicatifs.

**Critère de sortie**: base saine, endpoints et pages critiques fonctionnels.

## Phase 1 — Coeur CRM (5-7 jours)
- CRUD prospects complet.
- Gestion statuts opérationnelle.
- Notes et historique événementiel.
- Liste prospects avec recherche + filtres + pagination.

**Critère de sortie**: un commercial peut travailler 100% de son suivi dans l’outil.

## Phase 2 — Exploitabilité opérationnelle (3-4 jours)
- Import CSV robuste + rapport d’exécution.
- Dashboard branché sur données réelles.
- Finitions UX mobile-first (liste + fiche + actions principales).

**Critère de sortie**: onboarding des données existantes possible, pilotage quotidien possible.

## Phase 3 — Stabilisation avant prod (2-3 jours)
- Tests manuels guidés sur parcours critiques.
- Correction des bugs bloquants et durcissement sécurité.
- Documentation d’exploitation (installation, sauvegarde, rollback simple).

**Critère de sortie**: V1 déployable sereinement.

---

## E) Arborescence de navigation (menu principal)

Menu recommandé (ordre d’usage réel):

1. **Dashboard**
2. **Prospects**
3. **Pipeline**
4. **Import**
5. **Campagnes** (simple, optionnel si déjà prêt)
6. **Paramètres**
7. **Déconnexion**

Règles UX:
- Menu stable desktop + version compacte mobile.
- Actions principales visibles en haut : “Nouveau prospect”, “Importer CSV”.
- Aucun lien “factice” (si non prêt => badge “Bientôt disponible”, action désactivée).

---

## F) Écrans nécessaires pour le MVP

## 1) Écrans Auth
- Login.

## 2) Dashboard
- Cartes KPI.
- Bloc “Activités récentes”.
- Raccourcis actions.

## 3) Prospects
- **Liste prospects** (table responsive + filtres + recherche + pagination).
- **Créer prospect** (formulaire simple + validation).
- **Fiche prospect** (infos, stratégie, notes, historique, changement de statut).
- **Modifier prospect**.

## 4) Pipeline
- Vue colonnes par statut (ou liste groupée si charge technique).
- Drag & drop optionnel V1 (sinon changement via select).

## 5) Import
- Upload CSV.
- Mapping colonnes.
- Résultat d’import (succès/erreurs).

## 6) Campagnes simples (si maintenu en V1)
- Liste campagnes.
- Création/édition basique sans moteur d’envoi complexe.

## 7) Paramètres minimaux
- Sources, tags, statuts (CRUD minimal).
- Profil utilisateur (si déjà branché).

---

## G) Priorité technique de développement

Ordre recommandé (du plus critique au plus structurant):

1. **Fiabilité back-end**
   - Validation d’entrée centralisée.
   - Services métier clairs (ProspectService, StrategyService, ImportService).
   - Requêtes SQL indexées sur colonnes de recherche (status_id, source_id, created_at).

2. **Qualité des données**
   - Contraintes DB (unicité email si attendu, FK statuts/sources/tags).
   - Déduplication au moment de l’import.

3. **Cohérence UX**
   - Parcours principal sans impasse: Liste → Fiche → Action → Historique.
   - Mobile lisible sur les pages les plus fréquentes.

4. **Sécurité essentielle**
   - Sessions sécurisées, CSRF sur formulaires, échappement output HTML, politique mot de passe minimum.

5. **Observabilité simple**
   - Logs métier (création prospect, changement statut, erreur import).
   - Endpoint santé `/health` + journal d’erreurs exploitable.

---

## Module central détaillé : “Stratégie par prospect”

Ce module doit être la colonne vertébrale du CRM V1.

## Objectif
Permettre à l’utilisateur de définir, exécuter et suivre un mini-plan d’action pour chaque prospect, au-delà de simples champs statiques.

## Données minimales à stocker
- `prospect_id`
- `objectif_contact` (ex: obtenir RDV)
- `prochaine_action` (texte court)
- `date_prochaine_action`
- `canal_prioritaire` (email, téléphone, LinkedIn, autre)
- `niveau_priorite` (faible/moyenne/haute)
- `blocages` (texte libre)
- `derniere_interaction_at`
- `score_maturite` (0-100 optionnel simple)

## Comportement fonctionnel
1. Depuis la fiche prospect, section “Stratégie”.
2. L’utilisateur renseigne/édite le plan court terme.
3. À chaque mise à jour importante, entrée automatique dans l’historique.
4. La prochaine action alimente une vue “À relancer” (liste filtrée par date).
5. Les champs restent volontairement limités pour éviter la complexité CRM enterprise.

## Règles métier MVP
- `date_prochaine_action` obligatoire si `prochaine_action` non vide.
- Si statut passe à “Gagné” ou “Perdu”, la stratégie est figée en lecture seule (modifiable seulement par admin si besoin).
- Une relance en retard (> date du jour) remonte dans dashboard bloc “Actions en retard”.

## UI minimale
- Carte “Stratégie” dans la fiche prospect.
- Champs éditables inline (ou modal léger).
- Bouton “Marquer action comme faite” qui:
  - historise l’action,
  - vide/actualise `prochaine_action`,
  - met à jour `derniere_interaction_at`.

## Impact attendu
- Clarifie les prochaines étapes commerciales.
- Réduit les prospects “oubliés”.
- Donne une base solide pour futures automatisations (V2) sans dette structurelle.

---

## H) Risques à éviter

1. **Vouloir tout faire en V1**
   - Risque: produit lent à livrer, instable, adoption faible.

2. **Multiplier les écrans non branchés**
   - Risque: perte de confiance utilisateur immédiate.

3. **Négliger l’import CSV réel**
   - Risque: données corrompues, rejet de l’outil dès onboarding.

4. **Sous-estimer la qualité des données**
   - Risque: KPI faux, décisions business biaisées.

5. **Absence de stratégie par prospect structurée**
   - Risque: CRM passif (stockage), sans aide à l’exécution commerciale.

6. **Architecture trop ambitieuse trop tôt**
   - Risque: maintenance coûteuse pour une petite équipe.

7. **Oublier la version mobile utile**
   - Risque: baisse d’usage terrain, surtout en phase prospection active.

---

## Architecture simple et maintenable recommandée (Full PHP + MySQL)

## Structure
- `public/` : front controller, assets, `.htaccess`
- `src/Core/` : Router, Request, Response, DB, Session
- `src/Controllers/` : orchestration HTTP
- `src/Services/` : règles métier (Prospect, Strategy, Import, Campaign)
- `src/Models/` : accès données SQL/PDO
- `templates/` : vues PHP (pages + composants)
- `database/` : schéma + migrations SQL simples
- `storage/logs/` : logs applicatifs

## Principes d’implémentation
- Contrôleurs fins, services testables, modèles orientés requêtes.
- SQL explicite (pas d’ORM obligatoire en V1).
- DTO/arrays structurés entre couches pour éviter couplage des vues.
- Validation serveur systématique + sanitation output.
- Pagination et filtres côté SQL (pas côté template).

## Tables supplémentaires à prévoir pour “Stratégie par prospect”
- Option A (rapide): colonnes dans `prospects`.
- Option B (plus propre): table `prospect_strategies` (1-1 avec prospects).

Recommandation V1: **Option A** pour livrer vite, migration vers table dédiée en V2 si complexité croît.
