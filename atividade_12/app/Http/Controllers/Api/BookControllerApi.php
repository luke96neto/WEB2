<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookControllerApi extends Controller
{
    public function index()
    {
        return response()->json(Book::with(['author', 'category', 'publisher'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_id' => 'required|exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'published_year' => 'nullable|integer',
            'cover_image' => 'nullable|string',
        ]);

        $book = Book::create($validated);

        return response()->json($book, 201);
    }

    public function show($id)
    {
        $book = Book::with(['author', 'category', 'publisher'])->find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        return response()->json($book);
    }

    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author_id' => 'sometimes|exists:authors,id',
            'category_id' => 'sometimes|exists:categories,id',
            'publisher_id' => 'sometimes|exists:publishers,id',
            'published_year' => 'nullable|integer',
            'cover_image' => 'nullable|string',
        ]);

        $book->update($validated);

        return response()->json($book);
    }

    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Livro não encontrado'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Livro deletado com sucesso']);
    }
}
