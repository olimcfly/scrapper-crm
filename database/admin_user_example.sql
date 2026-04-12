-- Exemple d'admin initial
-- Mot de passe en clair utilisé pour générer ce hash: ChangeMe123!
-- Pensez à le changer immédiatement après la première connexion.

INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at, updated_at)
VALUES (
  'Admin',
  '',
  'admin@example.com',
  '$2y$12$TZobtnLFqihMU0SY8CUJquKlAxfD0wIMpTlAffyixUpbjh9iyM6xe',
  'admin',
  1,
  NOW(),
  NOW()
);
