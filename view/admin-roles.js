let roles = [];
const form = document.getElementById('formRole');

async function charger() {
  const r = await api('roles');
  if (!r.ok) { afficherMessage('erreur', r.message); return; }
  roles = r.roles;
  document.getElementById('corps').innerHTML = roles.length ? roles.map(x => `<tr>
    <td>${x.idRole}</td><td><b>${esc(x.nomRole)}</b></td><td>${x.nbUsers}</td>
    <td><div class="actions-ligne">
      <button class="btn btn-sm" data-act="modifier" data-id="${x.idRole}">Modifier</button>
      <button class="btn btn-sm btn-danger" data-act="supprimer" data-id="${x.idRole}">Supprimer</button>
    </div></td></tr>`).join('') : '<tr><td colspan="4" class="vide">Aucun rôle.</td></tr>';
}

function reinitialiser() {
  form.reset();
  form.elements.idRole.value = '';
  document.getElementById('titreForm').textContent = 'Ajouter un rôle';
  document.getElementById('btnAnnuler').style.display = 'none';
}

document.getElementById('btnAnnuler').addEventListener('click', reinitialiser);

document.getElementById('corps').addEventListener('click', async e => {
  const b = e.target.closest('button');
  if (!b) return;
  const x = roles.find(r => r.idRole == b.dataset.id);
  if (b.dataset.act === 'modifier') {
    form.elements.idRole.value = x.idRole;
    form.elements.nomRole.value = x.nomRole;
    document.getElementById('titreForm').textContent = 'Modifier le rôle #' + x.idRole;
    document.getElementById('btnAnnuler').style.display = 'inline-block';
    form.elements.nomRole.focus();
  }
  if (b.dataset.act === 'supprimer' && confirm('Supprimer le rôle « ' + x.nomRole + ' » ?')) {
    const fd = new FormData();
    fd.append('idRole', x.idRole);
    const r = await api('role_supprimer', fd);
    afficherMessage(r.ok ? 'succes' : 'erreur', r.message);
    charger();
  }
});

form.addEventListener('submit', async e => {
  e.preventDefault();
  if (form.elements.nomRole.value.trim() === '') { afficherMessage('erreur', 'Saisissez le nom du rôle.'); return; }
  const r = await api('role_enregistrer', new FormData(form));
  afficherMessage(r.ok ? 'succes' : 'erreur', r.message);
  if (r.ok) { reinitialiser(); charger(); }
});

gardePromise.then(g => { if (g.ok) charger(); });
