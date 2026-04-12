<?php
$citations = [
    '"Le toucher apaise le corps et laisse l’esprit respirer."',
    '"Prenez soin de votre énergie, elle façonne votre quotidien."',
    '"Chaque pause bien-être est une promesse de douceur envers soi."',
    '"Le calme intérieur commence par un instant accordé à votre corps."',
];
$randomCitation = $citations[array_rand($citations)];
?>

<style>
  .login-page {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #f7f3eb;
  }

  .login-visual {
    position: relative;
    min-height: 340px;
    display: flex;
    align-items: flex-end;
    padding: 42px;
    color: #fdfaf5;
    background-image:
      linear-gradient(170deg, rgba(23, 47, 38, 0.35), rgba(70, 93, 80, 0.52)),
      url('https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=1500&q=80');
    background-size: cover;
    background-position: center;
  }

  .login-visual::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(255, 251, 241, 0.08), rgba(42, 63, 52, 0.35));
  }

  .login-visual-content {
    position: relative;
    z-index: 1;
    max-width: 520px;
  }

  .login-brand {
    margin: 0;
    font-family: "Cormorant Garamond", Georgia, serif;
    font-size: clamp(2rem, 3vw, 3.1rem);
    font-weight: 600;
    letter-spacing: 0.4px;
  }

  .login-tagline {
    margin: 8px 0 0;
    font-size: 1.08rem;
    letter-spacing: 0.7px;
  }

  .login-quote {
    margin: 30px 0 0;
    max-width: 440px;
    font-size: 1.05rem;
    font-style: italic;
    line-height: 1.65;
    color: rgba(255, 250, 241, 0.95);
  }

  .login-form-area {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 42px 28px;
    background: #fdfaf4;
  }

  .login-card {
    width: 100%;
    max-width: 430px;
    padding: 40px 34px 28px;
    border-radius: 20px;
    background: #fffef9;
    border: 1px solid #ece5d9;
    box-shadow: 0 20px 45px rgba(105, 116, 95, 0.12);
  }

  .login-title {
    margin: 0;
    font-family: "Cormorant Garamond", Georgia, serif;
    font-size: 2rem;
    color: #2e4036;
  }

  .login-subtitle {
    margin: 8px 0 26px;
    color: #68776b;
    line-height: 1.5;
  }

  .login-field {
    margin-bottom: 16px;
  }

  .login-label {
    display: block;
    margin-bottom: 7px;
    color: #33483d;
    font-weight: 600;
  }

  .login-input {
    width: 100%;
    border: 1px solid #d8dfd2;
    border-radius: 12px;
    background: #fcfdf9;
    padding: 12px 14px;
    font-size: 0.98rem;
    color: #1f2937;
    transition: border-color 0.18s ease, box-shadow 0.18s ease;
  }

  .login-input:focus {
    outline: none;
    border-color: #92ad96;
    box-shadow: 0 0 0 3px rgba(146, 173, 150, 0.2);
  }

  .login-submit {
    width: 100%;
    margin-top: 8px;
    border: none;
    border-radius: 12px;
    padding: 13px 14px;
    background: linear-gradient(135deg, #758f73, #5d7c62);
    color: #f9f7f2;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
  }

  .login-submit:hover {
    filter: brightness(1.04);
  }

  .login-footnote {
    margin: 18px 0 0;
    text-align: center;
    font-size: 0.88rem;
    color: #7a8476;
    letter-spacing: 0.25px;
  }

  .login-errors {
    background: #fef1ef;
    border: 1px solid #f3d0ca;
    color: #8f2f25;
    border-radius: 12px;
    padding: 12px 14px;
    margin-bottom: 16px;
  }

  .login-errors ul {
    margin: 8px 0 0 18px;
    padding: 0;
  }

  @media (max-width: 900px) {
    .login-page {
      grid-template-columns: 1fr;
      grid-template-rows: minmax(320px, 42vh) auto;
    }

    .login-visual {
      padding: 30px 24px;
      align-items: flex-end;
    }

    .login-form-area {
      padding: 24px;
      align-items: flex-start;
    }

    .login-card {
      max-width: 560px;
    }
  }
</style>

<div class="login-page">
  <section class="login-visual" aria-label="Univers bien-être Coralie Montreuil">
    <div class="login-visual-content">
      <h1 class="login-brand">Coralie Montreuil</h1>
      <p class="login-tagline">Massage &amp; Bien-être</p>
      <p class="login-quote"><?= htmlspecialchars($randomCitation) ?></p>
    </div>
  </section>

  <section class="login-form-area">
    <div class="login-card">
      <h2 class="login-title">Espace professionnel</h2>
      <p class="login-subtitle">Bienvenue dans votre espace de gestion, conçu pour travailler avec sérénité.</p>

      <?php if (!empty($errors)): ?>
        <div class="login-errors">
          <strong>Impossible de vous connecter :</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="/login" novalidate>
        <div class="login-field">
          <label class="login-label" for="email">Email professionnel</label>
          <input
            class="login-input"
            id="email"
            type="email"
            name="email"
            autocomplete="email"
            required
            value="<?= htmlspecialchars((string) ($old['email'] ?? '')) ?>"
          >
        </div>

        <div class="login-field">
          <label class="login-label" for="password">Mot de passe</label>
          <input
            class="login-input"
            id="password"
            type="password"
            name="password"
            autocomplete="current-password"
            required
          >
        </div>

        <button type="submit" class="login-submit">Accéder à mon espace</button>
      </form>

      <p class="login-footnote">Espace réservé – Bien-être &amp; Sérénité</p>
    </div>
  </section>
</div>
