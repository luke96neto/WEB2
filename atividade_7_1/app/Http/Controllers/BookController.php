<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function createWithId()
    {
        return view('books.create-id');
    }

    public function storeWithId(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'publisher_id'   => 'required|exists:publishers,id',
            'author_id'      => 'required|exists:authors,id',
            'category_id'    => 'required|exists:categories,id',
            'published_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'cover_image'    => 'nullable|image|max:2048',
        ]);

        $bookData = $request->except('cover_image');

        if ($request->hasFile('cover_image')) {
            $bookData['cover_image'] = $request->file('cover_image')->store('book_covers', 'public');
        } else {
            // Garante que o caminho da imagem padrão seja salvo no banco de dados
            $bookData['cover_image'] = 'images/no_imageF.jpeg';
        }

        Book::create($bookData);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso (Com ID).');
    }

    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    public function storeWithSelect(Request $request)
    {
        // === ATENÇÃO: VALIDAÇÃO E LÓGICA DE CAPA UNIFICADAS COM storeWithId ===
        $request->validate([
            'title'          => 'required|string|max:255',
            'publisher_id'   => 'required|exists:publishers,id',
            'author_id'      => 'required|exists:authors,id',
            'category_id'    => 'required|exists:categories,id',
            // Adicionado validação para published_year (estava faltando)
            'published_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            // Adicionado validação para cover_image (estava faltando)
            'cover_image'    => 'nullable|image|max:2048',
        ]);

        $bookData = $request->except('cover_image');

        if ($request->hasFile('cover_image')) {
            $bookData['cover_image'] = $request->file('cover_image')->store('book_covers', 'public');
        } else {
            // Garante que o caminho da imagem padrão seja salvo no banco de dados
            $bookData['cover_image'] = 'images/no_imageF.jpeg';
        }

        Book::create($bookData);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso (Com Select).');
    }

    public function edit(Book $book)
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'publisher_id'   => 'required|exists:publishers,id',
            'author_id'      => 'required|exists:authors,id',
            'category_id'    => 'required|exists:categories,id',
            'published_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'cover_image'    => 'nullable|image|max:2048',
            'delete_cover_image' => 'boolean',
        ]);

        $bookData = $request->except(['cover_image', 'delete_cover_image']);

        if ($request->hasFile('cover_image')) {
            // Nova imagem enviada: apaga a antiga (se não for a padrão) e salva a nova
            if ($book->cover_image && $book->cover_image !== 'images/no_imageF.jpeg' && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $bookData['cover_image'] = $request->file('cover_image')->store('book_covers', 'public');

        } elseif ($request->boolean('delete_cover_image')) {
            // Checkbox para deletar marcado: apaga a antiga (se não for a padrão) e define para padrão
            if ($book->cover_image && $book->cover_image !== 'images/no_imageF.jpeg' && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $bookData['cover_image'] = 'images/no_imageF.jpeg';

        } else {
            // Nenhuma nova imagem enviada E checkbox de deletar NÃO marcado:
            // Mantém o valor atual de cover_image do livro (do banco de dados)
            // === NOVO: OPERADOR NULL COALESCE para garantir que 'images/no_imageF.jpeg' seja usado se o DB for NULL/vazio ===
            $bookData['cover_image'] = $book->cover_image ?: 'images/no_imageF.jpeg';
        }

        $book->update($bookData);

        return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso.');
    }

    public function show(Book $book)
    {
        $book->load(['author', 'publisher', 'category']);
        $users = User::all();
        return view('books.show', compact('book','users'));
    }

    public function index()
    {
        $books = Book::with('author')->paginate(20);
        return view('books.index', compact('books'));
    }

    public function destroy(Book $book)
    {
        // O evento 'deleting' no modelo Book cuida da exclusão da imagem física.
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso.');
    }
}