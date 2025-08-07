<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    // Formulário com input de ID
    public function createWithId()
    {
        return view('books.create-id');
    }

    // Salvar livro com input de ID
    public function storeWithId(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'publisher_id' => 'required|exists:publishers,id',
        'author_id' => 'required|exists:authors,id',
        'category_id' => 'required|exists:categories,id',
        'cover_image' => 'nullable|image|max:2048',
    ]);

    if ($request->hasFile('cover_image')) {
        $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
    }

    Book::create($validated);

    return redirect()->route('books.index')->with('success', 'Livro criado com sucesso!');
}


    // Formulário com input select
    public function createWithSelect()
    {
        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        Book::create($request->all());
        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    public function edit(Book $book)
    {

        $this->authorize('update', $book);

        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book', 'publishers', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'title' => 'required',
            'author_id' => 'required',
            'publisher_id' => 'required',
            'category_id' => 'required',
            'cover_image' => 'nullable|image|max:2048',
        ]);
    
        if ($request->hasFile('cover_image')) {
            // deletar a imagem antiga
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
    
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }
    
        $book->update($validated);
    
        return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso!');
    }
    

    public function show(Book $book)
{
    $this->authorize('view', $book);

    $book->load(['author', 'publisher', 'category']);
    $users = User::all();

    // Verifica se há empréstimo aberto para o livro
    $emprestimoAberto = DB::table('borrowings')
        ->where('book_id', $book->id)
        ->whereNull('returned_at')
        ->exists();

    return view('books.show', compact('book', 'users', 'emprestimoAberto'));
}


    public function index()
    {
        $books = Book::with('author')->paginate(20);
        return view('books.index', compact('books'));
    }
    public function destroy(Book $book)
{
    if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
        Storage::disk('public')->delete($book->cover_image);
    }

    $book->delete();

    $this->authorize('delete', $book);

    return redirect()->route('books.index')->with('success', 'Livro deletado com sucesso!');
}

public function borrow(Request $request, Book $book)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    // Verifica se já existe um empréstimo em aberto (returned_at = null)
    $jaEmprestado = DB::table('borrowings')
    ->where('book_id', $book->id)
    ->whereNull('returned_at')
    ->exists();


    if ($jaEmprestado) {
        return back()->withErrors(['erro' => 'Este livro já está emprestado e ainda não foi devolvido.']);
    }

    // Registra o empréstimo
    $book->users()->attach($request->user_id, [
        'borrowed_at' => now(),
        'returned_at' => null,
    ]);

    return back()->with('success', 'Empréstimo registrado com sucesso!');
}


}
