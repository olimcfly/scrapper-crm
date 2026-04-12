# Audit de migration vers Full PHP + MySQL (V1)

## 1) Ce qui peut être conservé
- Le schéma métier CRM global (prospects, notes, statuts, tags, sources) reste valide pour un MVP.
- La logique métier “CRUD prospect + notes + statut + tags” est conservable.
- Les documents de cadrage produit (`AUDIT.md`) restent utiles comme backlog.

## 2) Ce qui doit être supprimé
- Toute la stack frontend Vite/React/TypeScript supprimée du runtime (fichiers `public/src`, configs Vite/Tailwind/TS, package npm).
- Les dépendances Node/Express supprimées.

## 3) Ce qui doit être réécrit en PHP
- Routage HTTP
- Contrôleurs CRUD
- Accès DB (PDO préparé)
- Validation serveur
- Vues/écrans de base (liste, détail, création, édition)

## 4) Structure cible retenue
- `/public` : point d’entrée + rewrite Apache
- `/config` : app + database
- `/src/Core` : bootstrap, router, request/response, db
- `/src/Controllers` : API + web controllers
- `/src/Models` : accès SQL
- `/src/Services` : validation + logging
- `/templates` : vues PHP
- `/storage/logs` : logs applicatifs
- `/database` : schéma SQL

## 5) Implémentation V1 commencée dans ce commit
- Base MVC légère fonctionnelle en PHP.
- Endpoints API JSON versionnés sous `/api`.
- Pages web PHP pour exploiter le CRM sans React :
  - liste prospects
  - création prospect
  - détail prospect
  - modification prospect
  - suppression prospect
  - ajout note
  - changement statut

## 6) Prochaines étapes V1 (immédiates)
1. Ajouter pagination + recherche sur la liste prospects.
2. Ajouter auth simple (session) côté PHP.
3. Ajouter CRUD complet tags/sources/statuts (create/update/delete).
4. Ajouter historique des changements de statut.
5. Renforcer sécurité (CSRF + rate-limit + headers sécurité).
