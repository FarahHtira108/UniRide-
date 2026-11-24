let utilisateurs = [];
let rolesListe = [];
let moi = 0;
const modal = document.getElementById('modal');
const form = document.getElementById('formUser');

async function charger() {
  const q = document.getElementById('recherche').value.trim();
  const st = document.getElementById('filtreStatut').value;
  const r = await api('utilisateurs&q=' + encodeURIComponent(q) + '&statut=' + st);
  if (!r.ok) { afficherMessage('erreur', r.message); return; }
  utilisateurs = r.utilisateurs; moi = r.moi;
  document.getElementById('compteur').textContent = utilisateurs.length + ' utilisateur(s)';
  document.getElementById('corps').innerHTML = utilisateurs.length ? utilisateurs.map(u => {
    const actif = u.statut.toLowerCase() === 'actif';
    return `<tr>
      <td>${u.idUser}</td>
      <td><b>${esc(u.prenom)} ${esc(u.nom)}</b><br><small>${esc(u.email)}</small></td>
      <td>${esc(u.telephone)}</td>
      <td>${esc(u.roles) || '<small>—</small>'}</td>
      <td>${esc(u.dateInscription)}</td>
      <td><span class="badge badge-${actif ? 'actif' : 'inactif'}">${esc(u.statut)}</span>
          ${u.emailVerifie == 1 ? '<span class="badge badge-dark">email ✔</span>' : ''}</td>
      <td><div class="actions-ligne">
        <button class="btn btn-sm" data-act="modifier" data-id="${u.idUser}">Modifier</button>
        <button class="btn btn-sm btn-gris" data-act="statut" data-id="${u.idUser}">${actif ? 'Désactiver' : 'Activer'}</button>
        <button class="btn btn-sm btn-danger" data-act="supprimer" data-id="${u.idUser}">Supprimer</button>
      </div></td></tr>`;
  }).join('') : '<tr><td colspan="7" class="vide">Aucun utilisateur trouvé.</td></tr>';
}

function ouvrirModal(u) {
  form.reset();
  document.getElementById('erreurJs').textContent = '';
  document.getElementById('modalTitre').textContent = u ? 'Modifier l’utilisateur' : 'Nouvel utilisateur';
  document.getElementById('aideMdp').textContent = u ? '(laisser vide pour ne pas changer)' : '(8 caractères minimum)';
  form.elements.idUser.value = u ? u.idUser : '';
  if (u) {
    ['nom', 'prenom', 'email', 'telephone', 'adresse'].forEach(c => form.elements[c].value = u[c] || '');
    form.elements.statut.value = u.statut.toLowerCase() === 'actif' ? 'actif' : 'inactif';
    form.elements.emailVerifie.checked = u.emailVerifie == 1;
  }
  const mesRoles = u && u.roleIds ? u.roleIds.split(',') : [];
  document.getElementById('rolesBox').innerHTML = rolesListe.length ? rolesListe.map(r =>
    `<label><input type="checkbox" name="roles[]" value="${r.idRole}" ${mesRoles.includes(String(r.idRole)) ? 'checked' : ''}> ${esc(r.nomRole)}</label>`
  ).join('') : '<small>Aucun rôle : créez-en dans la page Rôles.</small>';
  modal.classList.add('ouvert');
}
const fermerModal = () => modal.classList.remove('ouvert');

document.getElementById('btnNouveau').addEventListener('click', () => ouvrirModal(null));
document.getElementById('btnAnnuler').addEventListener('click', fermerModal);
modal.addEventListener('click', e => { if (e.target === modal) fermerModal(); });
document.getElementById('recherche').addEventListener('input', charger);
document.getElementById('filtreStatut').addEventListener('change', charger);

document.getElementById('corps').addEventListener('click', async e => {
  const b = e.target.closest('button');
  if (!b) return;
  const u = utilisateurs.find(x => x.idUser == b.dataset.id);
  if (b.dataset.act === 'modifier') ouvrirModal(u);
  if (b.dataset.act === 'statut') {
    const fd = new FormData();
    fd.append('idUser', u.idUser);
    fd.append('statut', u.statut.toLowerCase() === 'actif' ? 'inactif' : 'actif');
    const r = await api('utilisateur_statut', fd);
    afficherMessage(r.ok ? 'succes' : 'erreur', r.message);
    charger();
  }
  if (b.dataset.act === 'supprimer' && confirm('Supprimer ' + u.prenom + ' ' + u.nom + ' ? Cette action est définitive.')) {
    const fd = new FormData();
    fd.append('idUser', u.idUser);
    const r = await api('utilisateur_supprimer', fd);
    afficherMessage(r.ok ? 'succes' : 'erreur', r.message);
    charger();
  }
});

form.addEventListener('submit', async e => {
  e.preventDefault();
  const err = document.getElementById('erreurJs');
  const g = n => form.elements[n].value.trim();
  const mdp = form.elements.password.value;
  const nouveau = !form.elements.idUser.value;
  if (g('nom') === '' || g('prenom') === '') { err.textContent = 'Nom et prénom obligatoires.'; return; }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(g('email'))) { err.textContent = 'Email invalide.'; return; }
  if (!/^[0-9+ ]{8,15}$/.test(g('telephone'))) { err.textContent = 'Téléphone invalide (8 à 15 chiffres).'; return; }
  if ((nouveau || mdp !== '') && mdp.length < 8) { err.textContent = 'Mot de passe : 8 caractères minimum.'; return; }
  err.textContent = '';
  const r = await api('utilisateur_enregistrer', new FormData(form));
  if (r.ok) { fermerModal(); afficherMessage('succes', r.message); charger(); }
  else { err.textContent = r.message; }
});

gardePromise.then(async g => {
  if (!g.ok) return;
  const r = await api('roles');
  if (r.ok) rolesListe = r.roles;
  charger();
});
