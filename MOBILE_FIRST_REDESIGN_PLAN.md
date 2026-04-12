# Refonte CRM IA Prospection — Mobile-first, accessible, app-like

## Vision produit
Transformer l’application en **web app mobile-first** avec une sensation proche du natif :
- navigation rapide au pouce,
- actions principales accessibles en 1–2 taps,
- interfaces épurées et lisibles,
- UX orientée terrain (consultation + action immédiate),
- continuité desktop sans sacrifier la productivité bureau.

---

## A) Architecture UX mobile + desktop

## 1) Schéma de navigation global

### Mobile (prioritaire)
- **Bottom navigation fixe (5 entrées max)** :
  1. Dashboard
  2. Prospects (collecte + liste)
  3. Stratégie
  4. Messages IA
  5. Pipeline
- **FAB contextuel** (floating action button) : “+ Prospect”, “+ Message”, “+ Note” selon écran.
- **Header compact sticky** : titre, recherche, menu secondaire.
- **Gestes tactiles** : swipe horizontal entre onglets d’une fiche prospect, swipe action sur cartes (archiver, relancer, tag).

### Desktop
- **Sidebar gauche** (navigation complète + secondaire):
  - Dashboard
  - Collecte profils
  - Stratégie par prospect
  - Génération contenu
  - Messages IA
  - Contacts
  - Pipeline
  - Paramètres
- **Top bar** : recherche globale, raccourcis IA, notifications.
- **Zones multitâches** : listes + panneau détail côte à côte pour productivité.

## 2) Architecture informationnelle (IA)
- Noyau “**Prospect-first**” : toute action part d’un prospect.
- Hiérarchie fonctionnelle :
  - Niveau 1 (usage quotidien) : Dashboard, Prospects, Stratégie, Messages, Pipeline.
  - Niveau 2 (support) : Génération contenu, Contacts.
  - Niveau 3 (admin/config) : Paramètres, modules en cours.

---

## B) Organisation des écrans principaux

## 1) Dashboard (mobile prioritaire)
- **Bloc “Aujourd’hui”** :
  - relances à faire,
  - prospects chauds,
  - messages en attente,
  - progression pipeline du jour.
- **KPI ultra-courts** (4 cartes max visible sans scroll).
- **Actions rapides** :
  - Relancer maintenant,
  - Générer message IA,
  - Ouvrir stratégie du prospect chaud,
  - Ajouter un prospect.
- **Feed d’activité** compact : dernière action par prospect.

## 2) Collecte profils
- Entrée simple : import, saisie manuelle, enrichissement.
- **Mobile** : flux en 3 étapes (source → mapping léger → validation).
- Résultat : cartes prospect nettoyées avec score de complétude.

## 3) Stratégie par prospect (module central)
- Écran structuré en sections :
  1. Contexte prospect (résumé)
  2. Objectif de conversion
  3. Angle de contact recommandé IA
  4. Prochaines actions (to-do)
  5. Historique stratégie
- Bouton principal : **“Générer prochaine action IA”**.

## 4) Génération contenu
- Templates orientés objectif (1er contact, relance, nurturing).
- Variables préremplies depuis fiche prospect.
- Prévisualisation mobile courte + mode “copier/envoyer”.

## 5) Messages IA
- UX type “assistant actionnable” :
  - prompt guidé,
  - proposition de message,
  - édition rapide,
  - validation.
- Boutons clairs : “Régénérer”, “Raccourcir”, “Personnaliser”, “Envoyer”.

## 6) Contacts
- Liste filtrable, centrée sur statut et prochaine action.
- Fiche contact en sections repliables (infos, interactions, tags).

## 7) Pipeline
- Mobile : cartes par étape avec scroll horizontal.
- Desktop : vue kanban + métriques de conversion.
- Actions rapides in-card : avancer/reculer, annoter, relancer.

## 8) Modules non développés
- Cartes propres avec badge **“En cours de développement”**.
- Inclure :
  - objectif du module,
  - bénéfice attendu,
  - CTA “Être notifié”.

---

## C) Composants UI recommandés

## 1) Navigation
- Bottom nav (mobile), sidebar (desktop), onglets secondaires, fil d’Ariane desktop.

## 2) Contenus
- **Card Prospect** (avatar, score, statut, prochaine action, CTA).
- **Card KPI** (titre court, valeur, variation).
- **Timeline interactions** (messages, notes, actions IA).
- **Section accordéon** pour détails longs.

## 3) Actions
- Boutons taille min **44x44 px** (idéal 48x48).
- CTA principal unique par écran.
- Menus d’actions rapides persistants sur mobile.

## 4) Formulaires
- Inputs larges, labels visibles, aides contextuelles courtes.
- Multi-steps pour formulaires complexes.
- Validation inline accessible + message d’erreur actionnable.

## 5) Feedback UX
- États : loading skeleton, vide guidé, succès, erreur claire.
- Toast non bloquant + confirmation persistante pour actions critiques.

## 6) Accessibilité
- Contraste renforcé, typo lisible, tailles dynamiques.
- Focus visible clavier.
- Labels ARIA / descriptions pour actions icônes.
- Navigation compatible lecteurs d’écran.

---

## D) Règles de responsive design

## Breakpoints recommandés
- `0–599`: mobile compact
- `600–899`: mobile large / petite tablette
- `900–1199`: tablette / desktop réduit
- `1200+`: desktop complet

## Règles clés
- Mobile-first CSS (progressive enhancement).
- Tableaux remplacés par cartes sous `900px`.
- Grille fluide 4/8/12 colonnes selon breakpoint.
- Zone “safe thumb” : actions principales dans le tiers bas mobile.
- Éviter modales complexes sur petit écran (préférer pages dédiées ou bottom sheets).

---

## E) Règles backend pour supporter l’usage mobile

## 1) Payload & performance
- Endpoints “mobile summary” dédiés : données essentielles seulement.
- Pagination systématique + curseur pour listes longues.
- Champs calculés côté serveur (next_action, priorité, score).
- Compression et cache HTTP agressif pour données stables.

## 2) Priorisation des données
- Retourner d’abord :
  - statut,
  - prochaine action,
  - dernière interaction,
  - score/urgence,
  - CTA recommandé.
- Différer données secondaires via endpoints détail.

## 3) Contrôleurs/pages
- Contrôleurs orientés **task-first** plutôt que data-first.
- DTO séparés “mobile card”, “desktop detail”.
- Réponses homogènes pour simplifier composants front.

## 4) Robustesse mobile
- Tolérance réseau faible : retries, timeouts courts, états dégradés.
- Idempotence des actions rapides (relance, changement d’étape).
- Journal d’événements léger pour reprise contextuelle.

---

## F) Priorisation visuelle des modules

## Priorité P1 (toujours visibles)
1. Dashboard
2. Collecte profils
3. Stratégie par prospect
4. Messages IA
5. Pipeline

## Priorité P2
6. Contacts
7. Génération contenu

## Priorité P3
8. Modules annexes / en cours

## Règle visuelle
- P1 : accessible depuis navigation principale.
- P2 : accessibles en second niveau et contextuellement depuis prospect.
- P3 : regroupés dans un hub “Bientôt disponible”.

---

## G) Plan de refonte page par page

## Phase 0 — Cadrage (1 semaine)
- Audit UX actuel (mobile/desktop).
- Cartographie des parcours critiques (collecte → stratégie → message → pipeline).
- Définition des KPI UX : temps pour action, taps nécessaires, taux d’achèvement.

## Phase 1 — Fondations design system (1–2 semaines)
- Tokens (couleurs, spacing, typo, radius, ombres).
- Bibliothèque composants (cards, buttons, inputs, bottom nav, sidebar, accordéons).
- Règles accessibilité (contraste, focus, aria).

## Phase 2 — Structure applicative (1 semaine)
- Layout responsive différencié mobile/desktop.
- Navigation globale (bottom nav + sidebar).
- États globaux (loading, erreurs, empty states).

## Phase 3 — Pages cœur P1 (3–4 semaines)
1. Dashboard mobile-first.
2. Prospects en cartes + filtres rapides.
3. Fiche prospect en sections (résumé, stratégie, interactions, pipeline).
4. Module stratégie central avec CTA IA.
5. Messages IA simplifiés (générer/éditer/envoyer).
6. Pipeline tactile mobile + kanban desktop.

## Phase 4 — Pages P2 (1–2 semaines)
- Contacts optimisés mobile.
- Génération contenu branchée au contexte prospect.

## Phase 5 — Modules non finalisés (0.5 semaine)
- Placeholders premium “En cours de développement”.
- CTA de feedback/utilisateurs pilotes.

## Phase 6 — Optimisation backend mobile (continue)
- Endpoints summary + détail.
- Performance/perf budget mobile.
- Monitoring temps de réponse et erreurs.

## Phase 7 — QA & accessibilité (1 semaine)
- Tests responsive multi-device.
- Audit a11y (clavier, lecteurs d’écran, contraste).
- Tests de parcours terrain (faible réseau).

---

## Décisions UX structurantes (recommandées)
- Toute page doit répondre à : **“Quelle est la prochaine meilleure action ?”**
- Une action primaire maximum par écran mobile.
- Les données secondaires ne doivent jamais bloquer l’action principale.
- Le module **Stratégie par prospect** devient le pivot entre collecte, contenu, messages et pipeline.
