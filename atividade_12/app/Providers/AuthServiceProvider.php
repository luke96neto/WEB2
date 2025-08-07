<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Importar Models e Policies
use App\Models\Book;
use App\Policies\BookPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * O mapeamento das policies para os modelos da aplicação.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Book::class => BookPolicy::class,
        // Exemplo para adicionar outras policies:
        // Author::class => AuthorPolicy::class,
        // User::class => UserPolicy::class,
    ];

    /**
     * Registra quaisquer serviços de autenticação/autorização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Você pode registrar Gates aqui, se necessário
        // Gate::define('admin-only', fn ($user) => $user->is_admin);
    }
}
