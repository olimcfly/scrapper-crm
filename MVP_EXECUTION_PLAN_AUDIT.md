# Audit CRM — périmètre MVP_EXECUTION_PLAN
_Date: 2026-04-12_

## Préambule critique
Le fichier `MVP_EXECUTION_PLAN.md` n'existe pas dans ce dépôt. Cet audit est donc basé sur :
1) le code réellement présent,
2) les fonctionnalités demandées,
3) les écarts observables vs un MVP CRM exploitable.

---

## 1) Liste prospects (recherche, filtres, pagination)

### État observé
- La liste prospects affiche un tableau simple avec bouton de création et lien vers la fiche. 
- Aucun champ de recherche, aucun filtre métier, aucune pagination serveur ou client.
- Le backend expose seulement `all()` trié par `updated_at` sans `LIMIT/OFFSET`, ni critères.

### Verdict
- **Solide:** affichage de base et navigation vers détail.
- **Fragile:** dès que le volume monte (> quelques centaines), UX et perf vont se dégrader.
- **Bugs potentiels:** timeouts/perf DB, page inutilisable en mobile, absence de tri explicite utilisateur.

---

## 2) Fiche prospect

### État observé
- La fiche montre infos principales, changement de statut, ajout de note, historique des notes.
- Les actions create/update utilisent un validateur dédié (email + score + champs requis).
- Les sorties HTML utilisent `htmlspecialchars`.

### Verdict
- **Solide:** structure CRUD cohérente, validations minimales utiles.
- **Fragile:** pas d'entité "stratégie prospect" ; les tags existent techniquement mais ne sont pas réellement pilotés dans l'UI de fiche.
- **Bugs potentiels:** état incomplet des tags (synchronisation backend sans parcours UX complet), absence de garde CSRF sur formulaires de modification/suppression.

---

## 3) Historique métier

### État observé
- L'"historique" visible n'est que l'historique des notes (`prospect_notes`).
- Les changements de statut ne sont pas historisés dans une table d'événements.
- Aucun journal d'activité métier global (actions utilisateur, transitions de pipeline, import, etc.).

### Verdict
- **Solide:** journal de notes simple et exploitable.
- **Fragile:** impossible d'auditer le cycle commercial réel d'un prospect.
- **Bugs potentiels:** perte de traçabilité (qui a fait quoi, quand), blocant pour conformité/process sales.

---

## 4) Stratégie par prospect

### État observé
- Aucune structure explicite de stratégie (objectif, next step, échéance, canal prioritaire, owner, playbook).
- Le champ `notes_summary` peut servir de contournement, mais sans workflow.

### Verdict
- **Solide:** base de données suffisamment ouverte pour ajouter cette capacité.
- **Fragile:** fonctionnalité absente fonctionnellement.
- **Bugs potentiels:** équipes forcent la stratégie en texte libre => hétérogénéité, impossible à requêter.

---

## 5) Import CSV

### État observé
- Aucune route, contrôleur ou service d'import CSV dans ce backend PHP.
- Aucun parseur CSV, aucun mapping colonnes, aucun rapport d'erreurs d'import.

### Verdict
- **Solide:** rien (fonctionnalité non livrée dans ce codebase).
- **Fragile:** promesse MVP non tenue si l'import fait partie du scope attendu.
- **Bugs potentiels:** dépendance à des imports manuels externes non tracés.

---

## 6) Sécurité

### Points positifs
- Requêtes SQL préparées (limite le risque d'injection SQL).
- `password_verify` pour l'authentification.
- `session_regenerate_id` après login/logout.
- Encodage HTML côté templates.

### Failles / risques majeurs
- **CORS totalement ouvert** (`Access-Control-Allow-Origin: *`) appliqué globalement.
- **Absence de protection CSRF** sur formulaires web (login, create/edit/delete, notes, status).
- **API prospects non protégée par authentification** (`/api/prospects*` accessible sans contrôle).
- Session cookie `secure` dépend d'env et peut être false en prod si mal configuré.
- Aucun rate limiting / lockout sur login.

### Verdict
- **Solide:** socle PDO + hashing + escaping.
- **Fragile:** exposition API + CSRF + CORS = surface d'attaque trop large pour prod.

---

## 7) UX globale

### État observé
- UI simple et compréhensible sur desktop.
- Table brute non pensée pour mobile dense.
- Pas de feedback avancé (toasts, loaders, erreurs contextualisées).
- Le routeur retourne JSON 404 même pour routes web, ce qui nuit à l'expérience utilisateur.

### Verdict
- **Solide:** clarté minimale, parcours principal lisible.
- **Fragile:** pas au niveau d'un CRM prêt production pour utilisateurs non techniques.

---

## A) Ce qui est solide
1. CRUD prospect cœur fonctionnel (liste, fiche, création, édition, suppression).
2. Validation métier minimale mais utile (`first_name`, `last_name`, email, score).
3. Journal de notes de base.
4. Requêtes préparées + escaping HTML + hash de mot de passe vérifié.

## B) Ce qui est fragile
1. Liste prospects sans recherche/filtres/pagination.
2. Historique métier partiel (notes uniquement).
3. Stratégie prospect inexistante.
4. Import CSV absent dans ce backend.
5. UX non robuste au scale et mobile limité.

## C) Bugs potentiels
1. Dégradation perf forte sur `all()` sans pagination.
2. Modifications sensibles possibles via CSRF.
3. Exposition des données via endpoints API non authentifiés.
4. Incohérences fonctionnelles tags/stratégie (données sans parcours utilisateur complet).
5. 404 JSON sur pages web non trouvées (expérience cassée côté utilisateur final).

## D) Améliorations prioritaires

### P0 (avant tout)
1. Protéger toutes les routes API et web state-changing (auth + CSRF).
2. Restreindre CORS à des origines autorisées.
3. Ajouter pagination + recherche + filtres en backend (`/prospects`, `/api/prospects`).

### P1
4. Implémenter historique métier réel (table `prospect_events` + trail status/notes/import).
5. Implémenter stratégie par prospect (next action, due date, owner, priority).
6. Implémenter import CSV robuste (preview, mapping, validation ligne à ligne, rapport d'erreurs, idempotence).

### P2
7. Refonte UX progressive: mobile table pattern, feedback utilisateur, erreurs actionnables.
8. Gestion fine des permissions (rôles, ownership des prospects).

## E) Ce qui empêche une mise en prod
1. **Absence de recherche/filtres/pagination** sur la liste prospects (non conforme au besoin explicite).
2. **Absence d'import CSV implémenté** dans ce backend.
3. **Absence de stratégie par prospect** implémentée.
4. **Historique métier incomplet** (pas d'audit trail des actions clés).
5. **Risque sécurité élevé** (API non auth, CSRF absent, CORS global permissif).

## Conclusion sans complaisance
Dans l'état, le produit ressemble à un **prototype CRUD interne**, pas à un CRM MVP "production-ready". La base est récupérable rapidement, mais **la sécurité, la scalabilité de la liste prospects et les fonctionnalités cœur demandées (stratégie, import CSV, historique métier complet)** doivent être traitées avant déploiement.
