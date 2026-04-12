# AUDIT PRODUIT + TECHNIQUE — scrapper-crm
_Date : 12 avril 2026_

## 0) Mode exécution (objectif)
Ce document transforme l’audit en plan d’action opérationnel, **priorisé MVP**, sans refonte massive risquée.
Le périmètre produit est volontairement simple : **dashboard, liste prospects, fiche prospect, statuts, tags, notes, historique, campagnes email simples, import et base propre**.
Le produit doit rester **lisible, mobile-friendly et exploitable rapidement**.

---

## 1) Vue d’ensemble rapide

### Ce que fait déjà l’application
- Auth utilisateur (login/signup/reset) via Supabase.
- Navigation interne type SPA entre Dashboard, Prospects, Pipeline, Campaigns, Settings.
- CRUD partiel prospects (ajout, listing, détail, statut, notes, activités).
- Pipeline Kanban avec drag & drop des statuts.
- Gestion basique de campagnes (création / édition / listing).
- Import CSV simple.

### Ce qui est fonctionnel
- Base UX cohérente et moderne.
- Schéma Supabase correct pour démarrer un CRM solo.
- RLS en place sur les tables métier principales.

### Ce qui est incomplet / fragile
- Plusieurs CTA/écrans donnent une impression de “feature prête” alors que non persistée.
- Parsing CSV fragile (cas réels non gérés).
- Qualité outillage non clean (`lint` et `typecheck` en erreur).
- Logique data dispersée dans les pages (pas de couche services).

---

## 1.1) Couverture du coeur MVP (état réel)

| Bloc coeur MVP | État | Commentaire |
|---|---|---|
| Dashboard | 🟡 Partiel | Présent visuellement, mais certaines métriques/graphes sont statiques. |
| Liste prospects | 🟢 OK | Listing + filtres client-side + pagination locale. |
| Fiche prospect | 🟢 OK | Détail, notes, timeline, changement de statut. |
| Statuts | 🟢 OK | Statuts présents en liste, fiche et pipeline. |
| Tags | 🟡 Partiel | Affichés mais non gérés en UX complète (édition/normalisation limitées). |
| Notes | 🟢 OK | Ajout + affichage persistant. |
| Historique | 🟢 OK | Activités enregistrées pour notes/statuts. |
| Campagnes email simples | 🟡 Partiel | CRUD de campagnes OK, exécution email réelle absente. |
| Import | 🟡 Partiel | Fonctionnel en cas simple, non robuste sur CSV réels. |
| Base propre | 🟡 Partiel | Schéma sain + RLS, mais qualité front encore instable (lint/typecheck). |

---

## 2) Bugs, incohérences et dette technique — triés par priorité

## P0 — Urgent (bloque reprise propre)
1. **Lint/Typecheck cassés**
   - imports inutilisés + dépendances hooks manquantes.
   - Impact: base instable, dette qui grossit à chaque PR.

2. **CTA critique incohérent**
   - Dashboard: bouton “Add Prospect” route vers `campaigns`.
   - Impact: confusion produit immédiate.

3. **Sécurité/robustesse init Supabase insuffisante**
   - client créé sans validation des env vars.
   - Impact: crash runtime opaque + debugging coûteux.

4. **Lisibilité mobile incomplète (non pilotée explicitement)**
   - l’app est surtout pensée desktop; l’ergonomie mobile n’est pas cadrée comme exigence MVP.
   - Impact: adoption terrain dégradée si usage smartphone.

## P1 — Important (MVP fragile en prod)
4. **Parsing CSV naïf (`split(',')`)**
   - casse sur guillemets, virgules dans texte, encodage hétérogène.

5. **Actions “fictives” dans Settings**
   - Data Sources toggles, Save Profile, Custom Fields non branchés.
   - Impact: dette UX + perte de confiance utilisateur.

6. **Écriture Supabase directement dans les pages**
   - duplication fetch/update, erreurs non unifiées.

## P2 — Plus tard (optimisation)
7. **Composants pages volumineux**
   - maintenance et évolutivité difficiles.

8. **Recherche globale visuelle mais non fonctionnelle**
   - UX incomplète, mais non bloquante pour démarrer.

9. **KPI partiellement statiques**
   - risque d’interprétation business erronée.

---

## 3) Structure cible plus propre (sans usine à gaz)

```text
/public
  /src
    /app
      App.tsx
      routes.ts (facultatif)
    /components
      /layout
      /ui
      /prospects
      /campaigns
    /pages
      Dashboard.tsx
      ProspectsList.tsx
      ProspectDetail.tsx
      Pipeline.tsx
      Campaigns.tsx
      Settings.tsx
      LoginPage.tsx
    /context
      AppContext.tsx
    /services
      supabaseClient.ts
      prospects.service.ts
      campaigns.service.ts
      notes.service.ts
      activities.service.ts
      auth.service.ts
    /hooks
      useProspects.ts
      useCampaigns.ts
    /lib
      csv.ts
      format.ts
    /types
      index.ts
```

### Principes
- **Pages = orchestration UI**, pas de logique d’accès DB brute.
- **Services = source unique des requêtes Supabase**.
- **Hooks = gestion état/fetch local** (léger, sans refactor total).
- **Mobile-first pragmatique** : composants/tableaux doivent avoir un fallback lisible mobile.

---

## 4) Plan de refactor léger, réaliste, orienté MVP

## Sprint A (P0) — Stabilisation (1 à 2 jours)
- Corriger tous les `lint`/`typecheck` bloquants.
- Corriger routing “Add Prospect” sur Dashboard.
- Ajouter garde-fou env dans init Supabase.
- Garder le comportement actuel, sans refonte de navigation.
- Définir une baseline mobile (breakpoints mini + priorités d’affichage sur liste/fiche).

## Sprint B (P1) — Fiabilisation (2 à 4 jours)
- Introduire `src/services/` (prospects + campaigns d’abord).
- Remplacer parsing CSV par parser robuste (ou utilitaire dédié).
- Uniformiser messages d’erreur utilisateur (toast/alert simple).
- Désactiver explicitement les actions non branchées avec label “Bientôt disponible”.
- Finaliser gestion tags MVP (ajout/suppression simple, sans taxonomie complexe).

## Sprint C (P2) — Consolidation (plus tard)
- Extraire hooks pour pages lourdes.
- Brancher recherche globale réelle.
- Améliorer KPI (données temporelles réelles).

---

## 5) Fichiers à modifier en premier (ordre conseillé)
1. `public/src/pages/Dashboard.tsx` (CTA incohérent)
2. `public/src/lib/supabase.ts` (guard env)
3. `public/src/context/AppContext.tsx` (cleanup type/lint)
4. `public/src/pages/Pipeline.tsx` (unused import + assert non-null)
5. `public/src/pages/ProspectsList.tsx` + `public/src/pages/Campaigns.tsx` (hooks deps / services)
6. `public/src/pages/Settings.tsx` (CSV + actions fictives)
7. `public/src/pages/ProspectDetail.tsx` (UX tags + lisibilité mobile)
8. `public/src/components/Layout.tsx` + `public/src/components/Header.tsx` (mobile navigation lisible)

---

## 6) Contraintes de mise en œuvre
- Ne pas introduire de nouvelle brique lourde tant que P0 non clos.
- Pas de refactor transversal “big bang”.
- Chaque PR doit:
  - corriger un problème réel,
  - conserver le comportement visible existant (sauf bug fix volontaire),
  - garder build OK.
- Toujours vérifier au moins un rendu mobile (pas de régression d’usage).

---

## 7) Prompt exact à lancer pour corriger la priorité n°1

> **Prompt à copier-coller :**
>
> « Exécute la priorité P0 n°1 du projet scrapper-crm :
> 1) corrige toutes les erreurs bloquantes de lint et typecheck,
> 2) sans modifier le comportement fonctionnel,
> 3) en faisant le minimum de changements nécessaires,
> 4) puis lance `npm run lint`, `npm run typecheck`, `npm run build` dans `/workspace/scrapper-crm/public`.
> Donne-moi ensuite :
> - la liste précise des fichiers modifiés,
> - un résumé des corrections,
> - les outputs des 3 commandes,
> - et un diff propre prêt à commit. »

---

## 8) Definition of Done (MVP simple mais pro)

Le MVP est considéré “propre et exploitable” quand :
1. `lint`, `typecheck`, `build` passent.
2. Les écrans coeur (dashboard, liste, fiche, pipeline/campagnes) sont cohérents et sans faux CTA.
3. Import CSV robuste sur cas réalistes.
4. Statuts/tags/notes/historique fonctionnent sans comportement ambigu.
5. Parcours mobile de base (liste + fiche + actions principales) est lisible et utilisable.
