<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // Exibe uma lista de autores
    public function index()
    {
        $author = Author
    ::all();
        return view('author.index', compact('author'));
    }

    // Mostra o formulário para criar uma nova autor
    public function create()
    {
        return view('author.create');
    }

    // Armazena uma nova autor no banco de dados
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:author|max:255',
        ]);

        Author
    ::create($request->all());

        return redirect()->route('author.index')->with('success', 'autor criada com sucesso.');
    }

    // Exibe uma autor específica
    public function show(Author
 $Author
)
    {
        return view('author.show', compact('Author
    '));
    }

    // Mostra o formulário para editar uma autor existente
    public function edit(Author
 $Author
)
    {
        return view('author.edit', compact('Author
    '));
    }

    // Atualiza uma autor no banco de dados
    public function update(Request $request, Author
 $Author
)
    {
        $request->validate([
            'name' => 'required|string|unique:author,name,' . $Author
        ->id . '|max:255',
        ]);

        $Author
    ->update($request->all());

        return redirect()->route('author.index')->with('success', 'autor atualizada com sucesso.');
    }

    // Remove uma autor do banco de dados
    public function destroy(Author
 $Author
)
    {
        $Author
    ->delete();

        return redirect()->route('author.index')->with('success', 'autor excluída com sucesso.');
    }
}