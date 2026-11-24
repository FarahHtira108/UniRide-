if (new URLSearchParams(window.location.search).get('inscrit')) {
  afficherMessage('succes', 'Compte créé ! Vous pouvez vous connecter.');
}

const form = document.getElementById('formConnexion');
form.addEventListener('submit', async e => {
  e.preventDefault();
  if (!valider(form)) return;
  const r = await api('connexion', new FormData(form));
  if (!r.ok) { afficherMessage('erreur', r.message); return; }

  // Vérifie que la session PHP est bien conservée avant de changer de page
  const s = await api('session');
  if (s.ok) {
    window.location.href = 'profil.html';
  } else {
    afficherMessage('erreur', 'Connexion acceptée, mais la session PHP n\'est pas conservée. Vérifiez session.save_path dans php.ini et que les cookies sont autorisés.');
  }
});
