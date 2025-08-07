@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Débitos Pendentes</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($users->isEmpty())
        <p>Nenhum usuário com débito pendente.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Débito (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }} ({{ $user->email }})</td>
                        <td>{{ number_format($user->debit, 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('debitos.pay', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Confirmar pagamento e zerar débito?')">
                                    Quitar Débito
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
