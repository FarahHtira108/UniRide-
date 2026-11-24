// ===== Fonctions communes du back office =====

async function api(action, data) {
  const options = data ? { method: 'POST', body: data } : { method: 'GET' };
  try {
    const reponse = await fetch('../controleurs/AdminController.php?action=' + action, options);
    const texte = await reponse.text();
    try { return JSON.parse(texte); }
    catch (e) { return { ok: false, message: 'Erreur PHP : ' + texte.substring(0, 150) }; }
  } catch (e) {
    return { ok: false, message: 'Serveur injoignable. Vérifiez Apache et MySQL.' };
  }
}

// Protège le HTML inséré dynamiquement
function esc(t) {
  const d = document.createElement('div');
  d.textContent = t == null ? '' : t;
  return d.innerHTML;
}

function afficherMessage(type, texte) {
  const z = document.getElementById('message');
  z.className = 'alert alert-' + type;
  z.textContent = texte;
  z.style.display = 'block';
  clearTimeout(afficherMessage.t);
  afficherMessage.t = setTimeout(() => z.style.display = 'none', 4000);
}

// Garde : seuls les administrateurs connectés accèdent aux pages
const gardePromise = api('moi').then(r => {
  if (r.ok) {
    document.getElementById('adminNom').textContent = '👤 ' + r.nom;
    return r;
  }
  if (r.code === 'connexion') {            // pas connecté
    window.location.href = 'connexion.html';
  } else if (r.code === 'interdit') {      // connecté mais pas administrateur
    document.querySelector('.contenu').innerHTML =
      '<div class="panel"><h3>Accès refusé</h3><p>Votre compte n’a pas le rôle <b>Administrateur</b>.</p>' +
      '<p style="margin-top:10px">Dans phpMyAdmin, ajoutez ce rôle à votre utilisateur (tables <b>role</b> et <b>userrole</b>), ' +
      'puis déconnectez-vous et reconnectez-vous.</p><p style="margin-top:14px"><a href="index.html">← Retour au site</a></p></div>';
  } else {                                 // erreur technique : on l'affiche
    afficherMessage('erreur', r.message);
    document.getElementById('message').style.display = 'block';
    clearTimeout(afficherMessage.t);
  }
  return r;
});

document.getElementById('btnDeconnexion').addEventListener('click', async e => {
  e.preventDefault();
  await fetch('../controleurs/AuthController.php?action=deconnexion', { method: 'POST' });
  window.location.href = 'index.html';
});
