const form = document.getElementById('formInscription');
form.addEventListener('submit', async e => {
  e.preventDefault();
  if (!valider(form)) return;
  const r = await api('inscription', new FormData(form));
  if (r.ok) {
    window.location.href = 'connexion.html?inscrit=1';
  } else {
    afficherMessage('erreur', r.message);
  }
});
