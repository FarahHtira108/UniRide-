<?php
// Fichier de TEST : à supprimer après vérification
require_once __DIR__ . '/config.php';
header('Content-Type: text/html; charset=utf-8');
echo '<h2>Test de connexion UniRide</h2>';
try {
    $db = Config::getConnexion();
    echo '<p style="color:green">✔ Connexion à la base réussie.</p>';

    foreach (['appuser', 'role', 'userrole'] as $t) {
        try {
            $n = $db->query("SELECT COUNT(*) FROM $t")->fetchColumn();
            echo "<p style='color:green'>✔ Table <b>$t</b> trouvée ($n ligne(s))</p>";
        } catch (Exception $e) {
            echo "<p style='color:red'>✘ Table <b>$t</b> introuvable : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    echo '<h3>Colonnes de appuser</h3><ul>';
    foreach ($db->query("SHOW COLUMNS FROM appuser") as $c) {
        echo '<li>' . $c['Field'] . ' — ' . $c['Type'] . '</li>';
    }
    echo '</ul><h3>5 derniers utilisateurs</h3><table border="1" cellpadding="6"><tr><th>id</th><th>nom</th><th>prénom</th><th>email</th><th>date</th></tr>';
    foreach ($db->query("SELECT idUser, nom, prenom, email, dateInscription FROM appuser ORDER BY idUser DESC LIMIT 5") as $u) {
        echo "<tr><td>{$u['idUser']}</td><td>{$u['nom']}</td><td>{$u['prenom']}</td><td>{$u['email']}</td><td>{$u['dateInscription']}</td></tr>";
    }
    echo '</table><h3>Utilisateurs et rôles (qui est administrateur ?)</h3><table border="1" cellpadding="6"><tr><th>id</th><th>email</th><th>rôles</th></tr>';
    foreach ($db->query("SELECT u.idUser, u.email, GROUP_CONCAT(r.nomRole) AS roles FROM appuser u
                         LEFT JOIN userrole ur ON ur.idUser=u.idUser LEFT JOIN role r ON r.idRole=ur.idRole GROUP BY u.idUser, u.email") as $x) {
        echo "<tr><td>{$x['idUser']}</td><td>{$x['email']}</td><td>" . ($x['roles'] ?: '<i>aucun</i>') . "</td></tr>";
    }
    echo '</table>';
} catch (Exception $e) {
    echo '<p style="color:red">✘ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
}
