const form = document.getElementById('formProfil');

// Remplit la page avec les données de l'utilisateur connecté
sessionPromise.then(s => {
  if (!s.ok) { window.location.href = 'connexion.html'; return; }
  const u = s.user;
  ['nom', 'prenom', 'email', 'telephone', 'adresse'].forEach(c => form.elements[c].value = u[c] || '');
  document.getElementById('avatar').textContent = (u.prenom[0] + u.nom[0]).toUpperCase();
  document.getElementById('nomComplet').textContent = u.prenom + ' ' + u.nom;
  document.getElementById('infos').textContent = 'Inscrit le ' + u.dateInscription + ' · Statut : ' + u.statut;
  const zoneRoles = document.getElementById('roles');
  s.roles.forEach(r => {
    const b = document.createElement('span');
    b.className = 'badge badge-dark';
    b.textContent = r;
    zoneRoles.appendChild(b);
  });
});

form.addEventListener('submit', async e => {
  e.preventDefault();
  if (!valider(form)) return;
  const r = await api('profil', new FormData(form));
  afficherMessage(r.ok ? 'succes' : 'erreur', r.message);
  if (r.ok) {
    form.elements.password.value = '';
    document.getElementById('nomComplet').textContent = form.elements.prenom.value + ' ' + form.elements.nom.value;
  }
});
