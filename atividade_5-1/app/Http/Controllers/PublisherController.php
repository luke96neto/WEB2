<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    // Exibe uma lista de publisher
    public function index()
    {
        $publisher = Publisher::all();
        return view('publisher.index', compact('publisher'));
    }

    // Mostra o formulário para criar uma nova publisher
    public function create()
    {
        return view('publisher.create');
    }

    // Armazena uma nova publisher no banco de dados
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:publisher|max:255',
        ]);

        Publisher::create($request->all());

        return redirect()->route('publisher.index')->with('success', 'publisher criada com sucesso.');
    }

    // Exibe uma publisher específica
    public function show(Publisher $Publisher)
    {
        return view('publisher.show', compact('Publisher'));
    }

    // Mostra o formulário para editar uma publisher existente
    public function edit(Publisher $Publisher)
    {
        return view('publisher.edit', compact('Publisher'));
    }

    // Atualiza uma publisher no banco de dados
    public function update(Request $request, Publisher $Publisher)
    {
        $request->validate([
            'name' => 'required|string|unique:publisher,name,' . $Publisher->id . '|max:255',
        ]);

        $Publisher->update($request->all());

        return redirect()->route('publisher.index')->with('success', 'publisher atualizada com sucesso.');
    }

    // Remove uma publisher do banco de dados
    public function destroy(Publisher $Publisher)
    {
        $Publisher->delete();

        return redirect()->route('publisher.index')->with('success', 'publisher excluída com sucesso.');
    }
}