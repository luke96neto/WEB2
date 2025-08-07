<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // Exibe a lista de autores
    public function index()
    {
        $authors = Author::all();
        return view('authors.index', compact('authors'));
    }

    // Formulário para criar um novo autor
    public function create()
    {
        return view('authors.create');
    }

    // Salva um novo autor no banco
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        Author::create($request->all());

        return redirect()->route('authors.index')->with('success', 'Autor criado com sucesso.');
    }

    // Exibe detalhes de um autor específico
    public function show(Author $author)
    {
        return view('authors.show', compact('author'));
    }

    // Formulário para editar um autor existente
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    // Atualiza um autor no banco
    public function update(Request $request, Author $author)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        $author->update($request->all());

        return redirect()->route('authors.index')->with('success', 'Autor atualizado com sucesso.');
    }

    // Remove um autor do banco
    public function destroy(Author $author)
    {
        $author->delete();

        return redirect()->route('authors.index')->with('success', 'Autor excluído com sucesso.');
    }
}
