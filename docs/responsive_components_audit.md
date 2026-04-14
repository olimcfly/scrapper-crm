# Audit composants responsive (mobile-first)

## Navigation conservée (source unique)
- `templates/components/navigation/desktop_sidebar.php`
- `templates/components/navigation/topbar_compact.php`
- `templates/components/navigation/bottom_nav.php`

## Composants fusionnés (doublons logiques)
Les anciennes variantes ont été conservées comme alias vers la source unique :
- `templates/components/DesktopSidebar.php` → `navigation/desktop_sidebar.php`
- `templates/components/desktop_sidebar.php` → `navigation/desktop_sidebar.php`
- `templates/components/TopBarCompact.php` → `navigation/topbar_compact.php`
- `templates/components/topbar_compact.php` → `navigation/topbar_compact.php`
- `templates/components/BottomNav.php` → `navigation/bottom_nav.php`
- `templates/components/bottom_nav.php` → `navigation/bottom_nav.php`

## Principes responsive appliqués
- **Mobile-first** : shell en colonne par défaut, sidebar activée à partir de `992px`.
- **Navigation pouce** : bottom nav fixe avec zones tactiles larges.
- **Topbar compacte mobile** : typographie et paddings réduits.
- **Desktop propre** : sidebar sticky + topbar standard + disparition de la bottom nav.

## Écrans refondus
- Dashboard : structure orientée actions rapides et CTA principal visible.
- Prospects : liste en cartes + filtres en bottom sheet (conservé).
- Pipeline : colonnes adaptées en cartes verticales sur mobile.
