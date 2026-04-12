# Plan d’implémentation CRM IA Prospection
## Mobile-first, prospect-first, exécutable en sprints courts

## Objectif
Transformer la vision produit en **plan d’exécution concret** :
- page par page,
- composant par composant,
- data backend par écran,
- ordre de build précis,
- statut explicite de chaque module.

Contraintes structurantes :
- **Mobile-first** (l’écran téléphone dicte la structure).
- **Prospect-first** (toute action part d’un prospect).
- **Stratégie par prospect = pivot** (collecte → stratégie → contenu/messages → pipeline).
- **MVP cible** : collecte profils, stratégie, contenu, messages IA, contacts.

---

## 1) Matrice des écrans principaux (avec statut)

### Légende statut
- **Actif** : à livrer complètement dans l’itération en cours.
- **MVP** : à livrer avec périmètre réduit mais utilisable en production.
- **Placeholder** : écran non livré fonctionnellement, carte informative “bientôt disponible”.

| ID | Écran | Rôle métier | Mobile (priorité) | Desktop | Dépendances | Statut |
|---|---|---|---|---|---|---|
| SCR-01 | Dashboard | Orchestrer la journée commerciale | Carte “Aujourd’hui”, KPI compacts, quick actions | Vue synthèse + panneau activité | API summary global + tâches | **MVP** |
| SCR-02 | Prospects – Liste | Hub principal de travail prospect | Liste cartes + filtres rapides + recherche | Table + panneau détail | Collecte + score + next action | **Actif** |
| SCR-03 | Prospect – Fiche | Vue 360 d’un prospect | Onglets: Résumé / Stratégie / Interactions / Pipeline | Split view liste + détail | Données prospect consolidées | **Actif** |
| SCR-04 | Collecte profils | Entrée de nouveaux prospects | Stepper 3 étapes (source → mapping → validation) | Assistant import enrichi | Import + normalisation + dédup | **MVP** |
| SCR-05 | Stratégie par prospect | Définir l’angle et la prochaine action | Sections courtes + CTA IA principal | Vue enrichie historique | Moteur IA stratégie | **Actif (pivot)** |
| SCR-06 | Génération contenu | Produire assets textuels | Templates + preview mobile + copier | Édition plus large | Variables prospect + templates | **MVP** |
| SCR-07 | Messages IA | Créer/éditer/envoyer message | Flow guidé prompt → proposition → édition | Mode batch + historique | LLM + règles ton/persona | **Actif** |
| SCR-08 | Contacts | Gérer carnet de contacts actionnable | Cartes filtrables + prochaine action | Table filtrable | Référentiel contacts + tags | **MVP** |
| SCR-09 | Pipeline | Piloter avancement opportunités | Colonnes scroll horizontal + quick move | Kanban complet + métriques | Étapes pipeline + événements | **MVP** |
| SCR-10 | Paramètres | Configuration système | Écran secondaire | Écran secondaire | Settings | **Placeholder** |
| SCR-11 | Modules annexes | Future capabilities | Hub “Bientôt disponible” | Idem | N/A | **Placeholder** |

---

## 2) Bibliothèque de composants UI à créer

## 2.1 Foundation (Design System)
1. **TokenColor** (palette primaire, neutres, succès, warning, danger).
2. **TokenSpacing** (4/8/12/16/24/32).
3. **TokenTypography** (mobile-first: body lisible, titres courts).
4. **TokenRadius** (cards, inputs, bottom sheets).
5. **TokenElevation** (ombres légères, focus ring).

## 2.2 Composants de navigation
1. **BottomNav** (5 entrées max).
2. **DesktopSidebar** (navigation complète + badges statut).
3. **TopBarCompact** (titre, recherche, actions globales).
4. **TabSwitcher** (onglets fiche prospect).
5. **BreadcrumbDesktop** (contexte sur desktop).

## 2.3 Composants métier prospect-first
1. **ProspectCard**
   - Champs: nom, score, statut, prochaine action, dernier contact.
   - Actions: ouvrir, relancer, tagger.
2. **ProspectHeader** (identité + score + urgence + CTA).
3. **StrategyBlock** (objectif, angle, actions IA).
4. **NextBestActionCard** (recommandation unique prioritaire).
5. **InteractionTimeline** (messages, notes, actions, dates).
6. **PipelineStageChip** (étape + couleur + SLA).

## 2.4 Composants d’action
1. **PrimaryCTAButton** (48x48 touch target min).
2. **SecondaryActionRow** (actions contextuelles).
3. **FABContextual** (+ Prospect, + Message, + Note).
4. **QuickActionSheet** (mobile bottom sheet).
5. **InlineActionMenu** (desktop table/list).

## 2.5 Composants de formulaires
1. **TextInputLarge**, **SelectLarge**, **TagInput**.
2. **StepperForm** (collecte en 3 étapes).
3. **InlineValidationMessage** (erreur actionnable).
4. **AIAssistInput** (prompt guidé + suggestions).

## 2.6 Composants d’état UX
1. **LoadingSkeletonCard**.
2. **EmptyStateGuided** (CTA + texte orienté action).
3. **ToastFeedback**.
4. **PersistentConfirmationBar** (actions critiques).
5. **NetworkDegradedBanner**.

## 2.7 Priorité de build composants
- **P0 (obligatoire sprint 1–2)**: BottomNav, ProspectCard, PrimaryCTAButton, LoadingSkeletonCard, EmptyStateGuided.
- **P1 (sprint 2–3)**: StrategyBlock, InteractionTimeline, StepperForm, QuickActionSheet.
- **P2 (sprint 3+)**: BreadcrumbDesktop, InlineActionMenu, variantes avancées.

---

## 3) Architecture responsive mobile/desktop (implémentation)

## 3.1 Breakpoints
- `0–599` : mobile compact.
- `600–899` : mobile large / petite tablette.
- `900–1199` : tablette / desktop réduit.
- `1200+` : desktop complet.

## 3.2 Règles de layout
1. **Mobile-first CSS**: base pour `<900`, enrichissement progressif ensuite.
2. **Navigation**:
   - `<900`: BottomNav + FAB.
   - `>=900`: Sidebar + TopBar.
3. **Densité d’information**:
   - `<900`: 1 colonne, cartes empilées.
   - `>=900`: 2 zones (liste + détail) sur pages métier.
4. **Tableaux**:
   - `<900`: interdits, convertis en cartes actionnables.
   - `>=900`: autorisés pour productivité (contacts, pipeline, historique).
5. **Actions critiques**:
   - Mobile: zone basse (safe thumb).
   - Desktop: header local + actions inline.

## 3.3 Pattern par type de page
- **Pages hub (Dashboard, Prospects liste)**: cartes + filtres sticky.
- **Pages détail (Fiche prospect, Stratégie)**: sections accordéon mobile, panneaux desktop.
- **Pages édition (Messages, Contenu)**: éditeur compact mobile, vue large desktop.

---

## 4) Données backend nécessaires par écran

## 4.1 Contrat API global (principes)
- DTO séparés:
  - `MobileCardDTO` (léger, actionnable),
  - `DesktopDetailDTO` (complet).
- Pagination curseur systématique sur listes.
- Champs calculés côté serveur:
  - `next_action`, `priority_score`, `urgency_level`, `last_interaction_at`.
- Endpoints dédiés `summary` pour chargement initial mobile.

## 4.2 Matrice écran → endpoints/data

| Écran | Endpoint minimum | Données obligatoires | Données différées |
|---|---|---|---|
| Dashboard | `GET /api/mobile/dashboard-summary` | KPI jour, relances, prospects chauds, messages en attente | Historique complet activité |
| Prospects liste | `GET /api/prospects?cursor=` | id, nom, score, statut, next_action, last_interaction | notes longues, pièces jointes |
| Fiche prospect | `GET /api/prospects/{id}/summary` | profil, stratégie active, pipeline stage, interactions récentes | historique exhaustif |
| Collecte profils | `POST /api/imports`, `POST /api/imports/{id}/mapping` | source, mapping, validation, erreurs de ligne | logs techniques |
| Stratégie prospect | `GET/POST /api/prospects/{id}/strategy` | objectif, angle recommandé, actions suivantes, version courante | archive complète des versions |
| Génération contenu | `POST /api/content/generate` | type template, variables prospect, contenu généré | variantes secondaires |
| Messages IA | `POST /api/messages/generate`, `POST /api/messages/send` | prompt, proposition, canal, statut envoi | analytics détaillés |
| Contacts | `GET /api/contacts?cursor=` | identité, statut, tags, prochaine action | interactions historiques longues |
| Pipeline | `GET /api/pipeline/board` + `POST /api/pipeline/move` | colonnes, cartes, métriques clés, permissions move | analytics avancés conversion |

## 4.3 Exigences robustesse mobile
1. Timeout court + retry borné (2 tentatives max).
2. Idempotence pour actions rapides (`send`, `move`, `reminder`).
3. Journal d’événements minimal côté backend pour reprise contexte.
4. Gestion dégradée explicite (`partial_data=true`).

---

## 5) Ordre exact d’implémentation (phases courtes)

## Phase 0 (2–3 jours) — Cadrage exécutable
- Finaliser parcours critiques: collecte → stratégie → message → pipeline.
- Valider contrats API `summary/detail`.
- Écrire checklist QA mobile-first.

## Phase 1 (4–5 jours) — Fondations UI + Navigation
- Livrer tokens + composants P0.
- Implémenter shell responsive: BottomNav/FAB + Sidebar.
- Activer états globaux (loading, empty, erreur).

## Phase 2 (4–5 jours) — Prospects hub + Fiche
- Construire SCR-02 Prospects liste (mobile puis desktop).
- Construire SCR-03 Fiche prospect (onglets + sections).
- Brancher champs calculés backend (`next_action`, score, urgence).

## Phase 3 (4–5 jours) — Module pivot Stratégie
- Construire SCR-05 Stratégie par prospect.
- Ajouter CTA principal “Générer prochaine action IA”.
- Persister versions stratégie + historique minimal.

## Phase 4 (4–5 jours) — Messages IA + Génération contenu
- Construire SCR-07 Messages IA (flow guidé complet).
- Construire SCR-06 Génération contenu (templates MVP).
- Connecter variables prospect et actions copier/envoyer.

## Phase 5 (3–4 jours) — Collecte profils + Contacts
- Construire SCR-04 Collecte profils (stepper 3 étapes).
- Construire SCR-08 Contacts (liste filtrable + prochaine action).
- Stabiliser import, mapping léger, validation.

## Phase 6 (3–4 jours) — Dashboard + Pipeline MVP
- Construire SCR-01 Dashboard orienté action.
- Construire SCR-09 Pipeline mobile tactile + desktop kanban simple.
- Brancher métriques clés et actions move/relance.

## Phase 7 (2–3 jours) — Placeholders + durcissement
- Construire SCR-10/SCR-11 placeholders premium.
- Audit responsive + accessibilité (focus, contraste, lecteurs).
- Optimisation perf mobile (payload, cache, monitoring).

---

## 6) Statut des modules (actif / MVP / placeholder)

| Module | Statut cible | Justification | Critère de “Done” |
|---|---|---|---|
| Prospects (liste + fiche) | **Actif** | Cœur prospect-first, point d’entrée principal | Filtrer, ouvrir, agir en ≤2 taps |
| Stratégie par prospect | **Actif** | Pivot central du produit | Next best action générée + historisée |
| Messages IA | **Actif** | Action commerciale directe | Générer, éditer, envoyer sans friction |
| Collecte profils | **MVP** | Nécessaire au flux, complexité import progressive | Import + mapping léger + validation exploitable |
| Génération contenu | **MVP** | Complète Messages IA, mais périmètre réduit initial | Templates prioritaires + variables prospect |
| Contacts | **MVP** | Support opérationnel, non pivot initial | Liste filtrable + prochaine action visible |
| Dashboard | **MVP** | Pilotage quotidien synthétique | KPI + relances + quick actions stables |
| Pipeline | **MVP** | Suivi avancement essentiel mais simplifiable | Déplacer étape + annoter + relancer |
| Paramètres | **Placeholder** | Non critique MVP commercial | Carte informative + “être notifié” |
| Modules annexes | **Placeholder** | Hors périmètre MVP | Hub “bientôt disponible” propre |

---

## Backlog immédiat (10 tickets à ouvrir)
1. Créer `BottomNav` + routing mobile.
2. Créer `ProspectCard` + quick actions.
3. API `GET /api/prospects` avec `next_action` et score.
4. Écran Prospects liste mobile-first.
5. Écran Fiche prospect avec onglet Stratégie.
6. API `GET/POST /api/prospects/{id}/strategy`.
7. CTA IA “Générer prochaine action” (frontend + backend).
8. Flow Messages IA (generate/edit/send).
9. Stepper Collecte profils (source/mapping/validation).
10. Écran Dashboard summary + quick actions.

Ce backlog est l’ordre recommandé pour lancer le MVP **sans casser la logique prospect-first**.
