<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label">Nome</label>
        <input type="text" class="form-control" id="name" name="name"
               value="{{ old('name', $user->name) }}" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email"
               value="{{ old('email', $user->email) }}" required>
    </div>

    @if(auth()->user()->role === 'admin')
        <div class="mb-3">
            <label for="role" class="form-label">Papel</label>
            <select class="form-select" id="role" name="role">
                <option value="cliente" {{ $user->role === 'cliente' ? 'selected' : '' }}>Cliente</option>
                <option value="bibliotecario" {{ $user->role === 'bibliotecario' ? 'selected' : '' }}>Bibliotecário</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
    @endif

    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
