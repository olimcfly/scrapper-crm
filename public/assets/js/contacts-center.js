document.addEventListener('click', (event) => {
  const row = event.target.closest('.contact-row');
  if (!row) return;

  if (event.target.closest('.row-actions')) {
    event.stopPropagation();
    return;
  }

  const detailId = row.getAttribute('data-detail-target');
  if (!detailId) return;

  const detailRow = document.getElementById(detailId);
  if (!detailRow) return;

  const wasHidden = detailRow.hasAttribute('hidden');

  document.querySelectorAll('.contact-detail-row').forEach((node) => node.setAttribute('hidden', 'hidden'));
  document.querySelectorAll('.contact-row').forEach((node) => node.classList.remove('is-active'));

  if (wasHidden) {
    detailRow.removeAttribute('hidden');
    row.classList.add('is-active');
  }
});
