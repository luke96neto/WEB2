@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Usuário</h1>

    <!-- Informações do Usuário -->
    <div class="card mb-4">
        <div class="card-header">
            {{ $user->name }}
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
        </div>
    </div>

    <!-- Histórico de Empréstimos -->
    <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($user->books->isEmpty())
                <p>Este usuário não possui empréstimos registrados.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->books as $book)
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $book->id) }}">
                                        {{ $book->title }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($book->pivot->borrowed_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $book->pivot->returned_at 
                                        ? \Carbon\Carbon::parse($book->pivot->returned_at)->format('d/m/Y H:i') 
                                        : 'Em Aberto' }}
                                </td>
                                <td>
                                    @if(is_null($book->pivot->returned_at))
                                        <form action="{{ route('borrowings.return', $book->pivot->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-warning btn-sm">Devolver</button>
                                        </form>
                                    @else
                                        <span class="text-success">Devolvido</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
