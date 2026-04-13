<section class="card" style="padding:20px;">
  <p class="eyebrow" style="margin:0 0 8px;color:#475569;font-weight:700;">Module Stratégie prospect</p>
  <h2 style="margin:0 0 8px;">Analyse psychologique & marketing</h2>
  <p class="muted" style="margin:0;">Collez un profil prospect, générez l'analyse IA puis passez à la création de contenu/message.</p>
</section>

<section class="card" style="margin-top:12px;">
  <form id="strategy-analysis-form" method="post" action="/strategie/analyse" style="display:grid;gap:14px;">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">

    <label for="profile" style="font-weight:700;">Profil prospect (texte libre)</label>
    <textarea id="profile" name="profile" rows="8" placeholder="Ex: Coach sport indépendant, 34 ans, activité irrégulière..." required style="font-size:16px;"></textarea>

    <button type="submit" class="btn" style="width:100%;min-height:52px;font-size:16px;">Analyser le prospect</button>
  </form>
  <p id="analysis-error" style="display:none;color:#b91c1c;margin:12px 0 0;"></p>
  <p id="analysis-warning" style="display:none;color:#92400e;margin:8px 0 0;"></p>
</section>

<section id="analysis-result" class="card" style="display:none;">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;">
    <h3 style="margin:0;">Résultat de l’analyse</h3>
    <span id="awareness-badge" class="status-badge status-active" style="font-size:12px;padding:6px 12px;"></span>
  </div>

  <div style="margin-top:14px;display:grid;gap:14px;">
    <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
      <h4 style="margin:0 0 8px;">Résumé</h4>
      <p id="summary" style="margin:0;"></p>
    </article>

    <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
      <h4 style="margin:0 0 8px;">Pain points</h4>
      <ul id="pain-points" style="margin:0;padding-left:20px;"></ul>
    </article>

    <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
      <h4 style="margin:0 0 8px;">Désirs profonds</h4>
      <ul id="desires" style="margin:0;padding-left:20px;"></ul>
    </article>

    <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
      <h4 style="margin:0 0 8px;">Angles de contenu</h4>
      <ul id="content-angles" style="margin:0;padding-left:20px;"></ul>
    </article>

    <article style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;">
      <h4 style="margin:0 0 8px;">Hooks marketing</h4>
      <ul id="recommended-hooks" style="margin:0;padding-left:20px;"></ul>
    </article>
  </div>

  <div style="display:grid;grid-template-columns:1fr;gap:10px;margin-top:16px;">
    <button id="go-to-content" class="btn" type="button" style="width:100%;">Générer du contenu</button>
    <a class="btn secondary" href="/messages-ia" style="width:100%;">Créer message</a>
  </div>
</section>

<script>
  (function () {
    var form = document.getElementById('strategy-analysis-form');
    if (!form) return;

    var errorBox = document.getElementById('analysis-error');
    var warningBox = document.getElementById('analysis-warning');
    var resultBox = document.getElementById('analysis-result');
    var badge = document.getElementById('awareness-badge');
    var contentButton = document.getElementById('go-to-content');
    var latestAnalysis = null;

    function fillList(id, items) {
      var list = document.getElementById(id);
      if (!list) return;
      list.innerHTML = '';

      if (!Array.isArray(items) || items.length === 0) {
        var empty = document.createElement('li');
        empty.textContent = 'Aucun élément.';
        list.appendChild(empty);
        return;
      }

      items.forEach(function (item) {
        var li = document.createElement('li');
        li.textContent = item;
        list.appendChild(li);
      });
    }

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      errorBox.style.display = 'none';
      warningBox.style.display = 'none';
      resultBox.style.display = 'none';

      var formData = new FormData(form);
      var submitButton = form.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Analyse en cours...';
      }

      fetch('/strategie/analyse', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function (response) {
        return response.text().then(function (rawBody) {
          var body = {};
          try {
            body = rawBody ? JSON.parse(rawBody) : {};
          } catch (e) {
            throw new Error('Réponse serveur invalide (JSON non lisible).');
          }

          if (!response.ok) {
            throw new Error(body.error || 'Erreur inconnue');
          }
          return body;
        });
      })
      .then(function (payload) {
        var data = payload.data || {};
        latestAnalysis = data;
        badge.textContent = data.awareness_level || 'N/A';
        document.getElementById('summary').textContent = data.summary || 'Aucun résumé';

        fillList('pain-points', data.pain_points || []);
        fillList('desires', data.desires || []);
        fillList('content-angles', data.content_angles || []);
        fillList('recommended-hooks', data.recommended_hooks || []);

        if (payload.meta && payload.meta.warning) {
          warningBox.textContent = payload.meta.warning;
          warningBox.style.display = 'block';
        }

        resultBox.style.display = 'block';
        resultBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
      })
      .catch(function (error) {
        errorBox.textContent = error.message;
        errorBox.style.display = 'block';
      })
      .finally(function () {
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.textContent = 'Analyser le prospect';
        }
      });
    });

    if (contentButton) {
      contentButton.addEventListener('click', function () {
        if (!latestAnalysis) {
          errorBox.textContent = 'Veuillez d’abord analyser un prospect.';
          errorBox.style.display = 'block';
          return;
        }

        contentButton.disabled = true;
        contentButton.textContent = 'Préparation...';

        fetch('/strategie/vers-contenu', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            _csrf: form.querySelector('input[name="_csrf"]').value,
            analysis: latestAnalysis
          })
        })
        .then(function (response) {
          return response.json().then(function (body) {
            if (!response.ok) {
              throw new Error((body && body.error) ? body.error : 'Impossible d’ouvrir le module Contenu.');
            }
            return body;
          });
        })
        .then(function (payload) {
          window.location.href = payload.redirect_url || '/contenu';
        })
        .catch(function (error) {
          errorBox.textContent = error.message;
          errorBox.style.display = 'block';
        })
        .finally(function () {
          contentButton.disabled = false;
          contentButton.textContent = 'Générer du contenu';
        });
      });
    }
  })();
</script>
