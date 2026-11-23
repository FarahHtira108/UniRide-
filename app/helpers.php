<?php
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function navActive(string $page, string $current): string { return $page === $current ? 'active' : ''; }
function hasRole(string $r): bool { return in_array($r, $_SESSION['user']['roles'] ?? [], true); }
