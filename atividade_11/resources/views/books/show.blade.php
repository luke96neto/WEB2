@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Livro</h1>

    <!-- Mensagens de sucesso ou erro -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Imagem da capa do livro -->
    <div class="mb-4 text-center">
        <img 
            src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-cover.jpg') }}" 
            alt="Capa do livro" 
            class="img-thumbnail" 
            style="max-width: 200px;">
    </div>

    <!-- Detalhes do Livro -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <span><strong>Título:</strong> {{ $book->title }}</span>

            @can('update', $book)
                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-primary">Editar</a>
            @endcan
        </div>
        <div class="card-body">
            <p><strong>Autor:</strong>
                <a href="{{ route('authors.show', $book->author->id) }}">
                    {{ $book->author->name }}
                </a>
            </p>
            <p><strong>Editora:</strong>
                <a href="{{ route('publishers.show', $book->publisher->id) }}">
                    {{ $book->publisher->name }}
                </a>
            </p>
            <p><strong>Categoria:</strong>
                <a href="{{ route('categories.show', $book->category->id) }}">
                    {{ $book->category->name }}
                </a>
            </p>

            @can('delete', $book)
                <form action="{{ route('books.destroy', $book) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                        Deletar Livro
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <!-- Formulário para Empréstimos -->
    @can('update', $book)
        @if (!$emprestimoAberto)
            <div class="card mb-4">
                <div class="card-header">Registrar Empréstimo</div>
                <div class="card-body">
                    <form action="{{ route('books.borrow', $book) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuário</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" selected>Selecione um usuário</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Registrar Empréstimo</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-warning mb-4">
                Este livro já está emprestado e ainda não foi devolvido.
            </div>
        @endif
    @endcan

    <!-- Histórico de Empréstimos -->
    <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($book->users->isEmpty())
                <p>Nenhum empréstimo registrado.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($book->users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('users.show', $user->id) }}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($user->pivot->borrowed_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $user->pivot->returned_at 
                                        ? \Carbon\Carbon::parse($user->pivot->returned_at)->format('d/m/Y H:i') 
                                        : 'Em Aberto' }}
                                </td>
                                <td>
                                    @if(is_null($user->pivot->returned_at))
                                        @can('update', $book)
                                            <form action="{{ route('borrowings.return', $user->pivot->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-warning btn-sm">Devolver</button>
                                            </form>
                                        @else
                                            <span class="text-muted">Sem permissão</span>
                                        @endcan
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

    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection
