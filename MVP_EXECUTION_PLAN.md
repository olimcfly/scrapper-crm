# Plan d’exécution concret — MVP CRM (Full PHP + MySQL)

_Date : 12 avril 2026_
_Source de cadrage : `MVP_PLAN.md`_

Objectif: transformer le cadrage produit en **plan de delivery exécutable**, orienté MVP livrable sur O2Switch, sans suringénierie.

---

## A) Backlog priorisé (P0 → P2)

## P0 — indispensable pour livrer un MVP exploitable

### P0.1 — Liste prospects exploitable (recherche + filtres + pagination SQL)
- **Tâches techniques**
  1. Ajouter paramètres de query (`q`, `status_id`, `source_id`, `page`, `per_page`) sur route web + API.
  2. Implémenter requête SQL paginée + compteur total.
  3. Ajouter UI filtres dans la liste + pagination côté template.
- **Fichiers**
  - `src/Models/ProspectModel.php`
  - `src/Controllers/WebProspectController.php`
  - `src/Controllers/ProspectController.php`
  - `templates/prospects/list.php`
- **Tables impactées**
  - `prospects`, `prospect_statuses`, `sources`

### P0.2 — Historique métier minimal (timeline fiable)
- **Tâches techniques**
  1. Créer table `prospect_activities`.
  2. Journaliser: création prospect, changement de statut, ajout de note, mise à jour stratégie.
  3. Afficher timeline dans fiche prospect.
- **Fichiers**
  - `database/schema.sql`
  - `src/Models/ProspectActivityModel.php` (nouveau)
  - `src/Services/ActivityLogger.php` (nouveau)
  - `src/Controllers/WebProspectController.php`
  - `src/Controllers/ProspectController.php`
  - `templates/prospects/detail.php`
- **Tables impactées**
  - `prospect_activities` (nouvelle), `prospects`

### P0.3 — Module central “Stratégie par prospect” (version MVP)
- **Tâches techniques**
  1. Ajouter colonnes stratégie dans `prospects` (Option A du MVP_PLAN).
  2. Ajouter validation métier (`date_prochaine_action` requise si `prochaine_action` renseignée).
  3. Ajouter endpoints/méthodes web et API de mise à jour stratégie.
  4. Ajouter bloc “Stratégie” dans la fiche prospect (édition simple).
  5. Journaliser les updates stratégie dans l’historique.
- **Fichiers**
  - `database/schema.sql`
  - `src/Services/ProspectValidator.php`
  - `src/Models/ProspectModel.php`
  - `src/Controllers/WebProspectController.php`
  - `src/Controllers/ProspectController.php`
  - `public/index.php`
  - `templates/prospects/detail.php`
- **Tables impactées**
  - `prospects`, `prospect_activities`

### P0.4 — Import CSV robuste (sans librairie lourde)
- **Tâches techniques**
  1. Ajouter page d’import + upload CSV sécurisé.
  2. Parser CSV via `fgetcsv` (gestion séparateur/encodage basique).
  3. Ajouter prévisualisation + mapping minimal.
  4. Ajouter rapport d’import (ok/ko + motif par ligne).
- **Fichiers**
  - `public/index.php`
  - `src/Controllers/WebImportController.php` (nouveau)
  - `src/Services/CsvImportService.php` (nouveau)
  - `src/Services/ProspectValidator.php`
  - `templates/import/index.php` (nouveau)
  - `templates/import/result.php` (nouveau)
- **Tables impactées**
  - `prospects`, `sources`, `prospect_statuses`, `tags`, `prospect_tag`

### P0.5 — Sécurité minimum O2Switch-ready
- **Tâches techniques**
  1. CSRF token sur formulaires POST.
  2. Durcir session cookie (httponly/samesite/secure si HTTPS).
  3. Limiter taille upload CSV + whitelist MIME.
  4. Échapper systématiquement les sorties templates.
- **Fichiers**
  - `src/Core/Session.php`
  - `src/Core/Request.php`
  - `src/Controllers/*` (points POST)
  - `templates/*` (formulaires)
- **Tables impactées**
  - aucune (sécurité applicative)

## P1 — important juste après la mise en service MVP

### P1.1 — Gestion tags/sources/statuts depuis l’UI
- CRUD minimal pour éviter maintenance SQL manuelle.
- Fichiers: `public/index.php`, nouveaux controllers/models/templates settings.
- Tables: `tags`, `sources`, `prospect_statuses`.

### P1.2 — Dashboard dynamique réellement utile
- KPIs dynamiques + bloc “actions en retard”.
- Fichiers: `src/Controllers/WebDashboardController.php` (nouveau), `templates/dashboard/index.php` (nouveau), `src/Models/ProspectModel.php`.
- Tables: `prospects`, `prospect_activities`.

## P2 — optionnel (uniquement si marge)
- Campagnes email basiques (CRUD sans moteur d’automatisation).
- Optimisations UX mobile additionnelles non bloquantes.

---

## B) Découpage en sprints courts et réalistes

## Sprint 1 (5 jours) — “Prospection exploitable”
**But**: rendre la liste + fiche réellement opérationnelles.
- P0.1 Liste/recherche/filtres/pagination.
- P0.2 Historique métier minimal (sans stratégie encore).
- Correctifs UI de base sur fiche/liste.

**Livrable fin sprint**
- Un utilisateur peut lister, chercher, filtrer, consulter et suivre l’historique des prospects.

## Sprint 2 (5 jours) — “Stratégie par prospect + sécurité”
**But**: ajouter le cœur différenciant MVP sans complexifier l’architecture.
- P0.3 Module stratégie par prospect complet MVP.
- P0.5 Sécurité minimum (CSRF/session/output escaping prioritaire).

**Livrable fin sprint**
- Le prospect est piloté par plan d’action concret, avec règles métier et traçabilité.

## Sprint 3 (4 jours) — “Onboarding data”
**But**: rendre l’import de base fiable pour démarrer en réel.
- P0.4 Import CSV robuste + rapport d’import.
- Ajustements validation et mapping.

**Livrable fin sprint**
- On peut importer des prospects réels avec visibilité claire des erreurs.

## Sprint 4 (3 jours) — “Stabilisation pré-prod O2Switch”
**But**: fiabiliser avant go-live.
- Tests manuels parcours critiques.
- Optimisations requêtes SQL.
- Check déploiement mutualisé (logs, limites upload, erreurs PHP).

**Livrable fin sprint**
- MVP prêt à déployer sur O2Switch.

---

## C) Ordre exact de développement

1. **Modèle de données d’abord**
   - Ajouter `prospect_activities` + colonnes stratégie dans `prospects`.
2. **Couche modèle ensuite**
   - Requêtes paginées, compteurs, activités, update stratégie.
3. **Services métier**
   - Validation stratégie + logging activités + import CSV.
4. **Contrôleurs**
   - Exposer flux web + API pour liste, stratégie, import.
5. **Templates**
   - Liste paginée, fiche stratégie, timeline, écran import.
6. **Sécurité transverse**
   - CSRF/session/output escaping sur tous formulaires.
7. **Tests manuels E2E MVP**
   - Parcours: créer prospect → définir stratégie → ajouter note → changer statut → importer CSV.

---

## D) Dépendances entre modules

- **Historique (`prospect_activities`)** dépend des événements Prospects/Notes/Statuts/Stratégie.
- **Stratégie par prospect** dépend de la fiche prospect (UI) + validation + historique.
- **Dashboard “actions en retard”** dépend des champs stratégie (`date_prochaine_action`, statut).
- **Import CSV** dépend de ProspectValidator + lookups (`sources`, `statuses`, `tags`).
- **Sécurité CSRF/session** dépend des formulaires web et doit précéder la mise en production.

Dépendances critiques:
1. DB migrations avant code applicatif.
2. Services/validation avant UI finale.
3. Historique avant dashboard dynamique.

---

## E) Fichiers à créer/modifier en premier

## Priorité 1 — base data + domaine
1. `database/schema.sql` (table activités + colonnes stratégie + index)
2. `src/Models/ProspectModel.php` (pagination/filtre/update stratégie)
3. `src/Services/ProspectValidator.php` (règles stratégie)
4. `src/Models/ProspectActivityModel.php` (nouveau)
5. `src/Services/ActivityLogger.php` (nouveau)

## Priorité 2 — exposition web/api
6. `public/index.php` (routes stratégie/import)
7. `src/Controllers/WebProspectController.php`
8. `src/Controllers/ProspectController.php`
9. `src/Controllers/WebImportController.php` (nouveau)
10. `src/Services/CsvImportService.php` (nouveau)

## Priorité 3 — écrans
11. `templates/prospects/list.php`
12. `templates/prospects/detail.php`
13. `templates/import/index.php` (nouveau)
14. `templates/import/result.php` (nouveau)

## Priorité 4 — sécurité transverse
15. `src/Core/Session.php`
16. `src/Core/Request.php`
17. `templates/layout/header.php` et formulaires concernés

---

## F) Risques techniques à surveiller

1. **Évolution de schéma sans migration incrémentale**
- Risque: casse en production mutualisée.
- Garde-fou: script SQL idempotent + sauvegarde DB avant import.

2. **Pagination faite côté PHP au lieu SQL**
- Risque: lenteur dès quelques milliers de prospects.
- Garde-fou: `LIMIT/OFFSET` + indexes (`status_id`, `source_id`, `updated_at`).

3. **Historique incomplet (événements non journalisés)**
- Risque: perte de traçabilité commerciale.
- Garde-fou: centraliser journalisation via `ActivityLogger`.

4. **Import CSV trop permissif**
- Risque: pollution data et doublons.
- Garde-fou: validation stricte + rapport ligne par ligne + mode dry-run simple.

5. **Sécurité formulaires insuffisante**
- Risque: CSRF/XSS sur environnement exposé.
- Garde-fou: token CSRF + échappement HTML partout + validation serveur systématique.

6. **Dérive V2 (sur-fonctionnalisation)**
- Risque: délai allongé, MVP non livré.
- Garde-fou: refuser séquences email, scoring avancé, permissions fines avant go-live.

7. **Spécificités O2Switch ignorées**
- Risque: surprises au déploiement (upload limits, timeout, logs).
- Garde-fou: tester contraintes PHP ini réelles, taille CSV max, erreurs runtime en condition proche prod.

---

## Definition of Done MVP (exécution)

Le MVP est “livrable” quand les conditions suivantes sont vraies:
1. Liste prospects avec recherche/filtre/pagination SQL en production.
2. Fiche prospect avec notes, changement de statut, stratégie par prospect.
3. Historique des actions clés visible et cohérent.
4. Import CSV opérationnel avec rapport d’erreurs.
5. CSRF/session/output escaping en place sur tous formulaires critiques.
6. Parcours manuel complet validé sur environnement O2Switch de pré-prod.
