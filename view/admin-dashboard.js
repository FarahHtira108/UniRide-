gardePromise.then(async g => {
  if (!g.ok) return;
  const r = await api('stats');
  if (!r.ok) { afficherMessage('erreur', r.message); return; }
  const s = r.stats;
  document.getElementById('sTotal').textContent = s.total;
  document.getElementById('sActifs').textContent = s.actifs;
  document.getElementById('sInactifs').textContent = s.inactifs;
  document.getElementById('sMois').textContent = s.mois;
  document.getElementById('sVerifies').textContent = s.verifies;

  const max = Math.max(1, ...s.parRole.map(x => +x.n));
  document.getElementById('parRole').innerHTML = s.parRole.length
    ? s.parRole.map(x => `<div class="barre-ligne"><div><span>${esc(x.nomRole)}</span><b>${x.n}</b></div>
        <div class="barre"><i style="width:${(x.n / max) * 100}%"></i></div></div>`).join('')
    : '<p class="vide">Aucun rôle.</p>';

  document.getElementById('derniers').innerHTML = s.derniers.length
    ? s.derniers.map(u => `<tr><td>${esc(u.prenom)} ${esc(u.nom)}</td><td>${esc(u.email)}</td><td>${esc(u.dateInscription)}</td>
        <td><span class="badge badge-${u.statut.toLowerCase() === 'actif' ? 'actif' : 'inactif'}">${esc(u.statut)}</span></td></tr>`).join('')
    : '<tr><td colspan="4" class="vide">Aucun utilisateur.</td></tr>';
});
