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
    min-height: 100dvh;
    display: grid;
    grid-template-columns: 1fr;
    background:
      radial-gradient(circle at top, #fdfaf2 0%, #f6f1e7 48%, #efe8db 100%);
  }

  .login-visual {
    display: none;
    position: relative;
    min-height: 100dvh;
    align-items: flex-end;
    padding: clamp(36px, 4vw, 56px);
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
    padding: 18px;
  }

  .login-card {
    width: 100%;
    max-width: 460px;
    padding: 28px 22px 22px;
    border-radius: 24px;
    background: #fffef9;
    border: 1px solid #e9e1d3;
    box-shadow:
      0 10px 26px rgba(82, 96, 83, 0.1),
      0 2px 8px rgba(82, 96, 83, 0.08);
  }

  .login-title {
    margin: 0;
    font-family: "Cormorant Garamond", Georgia, serif;
    font-size: clamp(2rem, 3.1vw, 2.6rem);
    color: #2d3f34;
    line-height: 1.1;
    letter-spacing: 0.2px;
  }

  .login-subtitle {
    margin: 10px 0 28px;
    color: #5f6f64;
    line-height: 1.55;
    font-size: 1rem;
  }

  .login-field {
    margin-bottom: 18px;
  }

  .login-label {
    display: block;
    margin-bottom: 8px;
    color: #2d4135;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .login-input {
    width: 100%;
    border: 1px solid #cfd8c8;
    border-radius: 14px;
    background: #fdfdf9;
    padding: 14px 16px;
    font-size: 1rem;
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
    border-radius: 14px;
    padding: 14px 16px;
    background: linear-gradient(140deg, #6f8b6e, #57745c);
    color: #f9f7f2;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.2px;
    transition: transform 0.16s ease, filter 0.16s ease;
  }

  .login-submit:hover {
    filter: brightness(1.04);
    transform: translateY(-1px);
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

  @media (min-width: 768px) and (max-width: 1023px) {
    .login-page {
      grid-template-columns: 1fr;
    }

    .login-form-area {
      padding: 48px 28px;
    }

    .login-card {
      max-width: 620px;
      padding: 38px 36px 30px;
    }
  }

  @media (min-width: 1024px) {
    .login-page {
      grid-template-columns: minmax(420px, 1.1fr) minmax(520px, 1fr);
    }

    .login-visual {
      display: flex;
    }

    .login-form-area {
      padding: clamp(40px, 6vw, 84px);
      background: linear-gradient(180deg, #fbf8f1 0%, #f5efe4 100%);
    }

    .login-card {
      max-width: 640px;
      padding: clamp(42px, 4vw, 56px) clamp(38px, 4.2vw, 54px) 36px;
      border-radius: 28px;
      box-shadow:
        0 28px 56px rgba(67, 82, 69, 0.16),
        0 6px 16px rgba(67, 82, 69, 0.09);
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
      <p class="login-subtitle">Entrez votre email pour recevoir un code de connexion à usage unique.</p>

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
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
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

        <button type="submit" class="login-submit">Recevoir mon code de connexion</button>
      </form>

      <p class="login-footnote">Espace réservé – Bien-être &amp; Sérénité</p>
    </div>
  </section>
</div>
