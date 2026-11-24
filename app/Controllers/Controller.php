<?php
namespace App\Controllers;

abstract class Controller
{
    protected string $page  = 'home';
    protected string $title = 'UniRide - Covoiturage étudiant';

    protected function render(string $view, array $data = [], string $layout = 'front', ?string $scripts = null): void
    {
        $content      = $this->capture(__DIR__ . "/../Views/pages/$view.php", $data);
        $extraScripts = $scripts ? $this->capture(__DIR__ . "/../Views/pages/$scripts.php", $data) : '';
        $title = $this->title; $currentPage = $this->page;
        require __DIR__ . "/../Views/layout_$layout.php";
    }
    private function capture(string $file, array $data): string
    {
        extract($data); ob_start(); require $file; return ob_get_clean();
    }
    protected function redirect(string $page): void { header("Location: index.php?page=$page"); exit; }
    protected function requireLogin(): void { if (empty($_SESSION['user'])) $this->redirect('login'); }
    protected function requireRole(string $role): void
    {
        $this->requireLogin();
        if (!hasRole($role)) { http_response_code(403); exit('Accès refusé : rôle ' . $role . ' requis.'); }
    }
}
