// ===== Fonctions communes à toutes les pages =====

// Appel du PHP (retourne du JSON) avec messages d'erreur clairs
async function api(action, formData) {
  const options = formData ? { method: 'POST', body: formData } : { method: 'GET' };
  try {
    const reponse = await fetch('../controleurs/AuthController.php?action=' + action, options);
    const texte = await reponse.text();
    try {
      return JSON.parse(texte);
    } catch (e) {
      return { ok: false, message: 'Le PHP ne s\'exécute pas ou contient une erreur. Réponse reçue : ' + texte.substring(0, 150) +
               ' — Ouvrez la page avec http://localhost/UniRide/view/... (pas Live Server).' };
    }
  } catch (e) {
    return { ok: false, message: 'Impossible de joindre le serveur. Vérifiez qu\'Apache est démarré et ouvrez http://localhost/UniRide/view/' };
  }
}

// Message de succès / d'erreur
function afficherMessage(type, texte) {
  const zone = document.getElementById('message');
  if (!zone) return;
  zone.className = 'alert alert-' + type;
  zone.textContent = texte;
  zone.style.display = 'block';
}

// Validation côté client (le PHP revérifie tout)
function valider(form) {
  const err = document.getElementById('erreurJs');
  const g = n => form.elements[n] ? form.elements[n].value.trim() : '';
  const mdp = form.elements.password ? form.elements.password.value : '';
  form.querySelectorAll('input').forEach(i => i.classList.remove('invalide'));
  let msg = '', champ = null;

  if (form.elements.nom && (g('nom') === '' || g('prenom') === '')) { msg = 'Nom et prénom obligatoires.'; champ = form.elements.nom; }
  else if (form.elements.email && !form.elements.email.disabled && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(g('email'))) { msg = 'Email invalide.'; champ = form.elements.email; }
  else if (form.elements.telephone && !/^[0-9+ ]{8,15}$/.test(g('telephone'))) { msg = 'Téléphone invalide (8 à 15 chiffres).'; champ = form.elements.telephone; }
  else if (form.id === 'formInscription' && !form.querySelector('input[name="roles[]"]:checked')) { msg = 'Choisissez au moins un rôle : Utilisateur ou Conducteur.'; champ = form.querySelector('input[name="roles[]"]'); }
  else if (form.id !== 'formProfil' && mdp === '') { msg = 'Mot de passe obligatoire.'; champ = form.elements.password; }
  else if (form.id === 'formInscription' && mdp.length < 8) { msg = 'Mot de passe : 8 caractères minimum.'; champ = form.elements.password; }
  else if (form.id === 'formProfil' && mdp !== '' && mdp.length < 8) { msg = 'Mot de passe : 8 caractères minimum.'; champ = form.elements.password; }
  else if (form.elements.confirm && form.elements.confirm.value !== mdp) { msg = 'Les mots de passe ne correspondent pas.'; champ = form.elements.confirm; }

  if (msg) { err.textContent = msg; champ.classList.add('invalide'); champ.focus(); return false; }
  err.textContent = '';
  return true;
}

// Bouton « afficher / masquer » sur les champs mot de passe
document.querySelectorAll('input[type=password]').forEach(champ => {
  const boite = document.createElement('div');
  boite.className = 'pw';
  champ.parentNode.insertBefore(boite, champ);
  boite.appendChild(champ);
  const bouton = document.createElement('button');
  bouton.type = 'button';
  bouton.textContent = '👁';
  bouton.addEventListener('click', () => {
    champ.type = champ.type === 'password' ? 'text' : 'password';
  });
  boite.appendChild(bouton);
});

// Les liens <a href> sont dans le HTML ; le JS affiche seulement
// les liens « connecté » ou « visiteur » selon la session.
const sessionPromise = api('session');
sessionPromise.then(s => {
  if (s.ok) {
    document.body.classList.add('est-connecte');
    if (s.roles.some(r => r.toLowerCase().includes('admin'))) {
      document.body.classList.add('est-admin');
    }
  }
});

const btnDeconnexion = document.getElementById('btnDeconnexion');
if (btnDeconnexion) {
  btnDeconnexion.addEventListener('click', async e => {
    e.preventDefault();
    await api('deconnexion', new FormData());
    window.location.href = 'index.html';
  });
}
