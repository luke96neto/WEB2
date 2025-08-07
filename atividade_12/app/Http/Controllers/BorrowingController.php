<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // 🔒 Verifica se há débito pendente
        if ($user->debit > 0) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Usuário possui débito pendente e não pode realizar novos empréstimos.');
        }

        // 🔒 Verifica se o livro já está emprestado
        $emprestimoAberto = Borrowing::where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($emprestimoAberto) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este livro já está emprestado e ainda não foi devolvido.');
        }

        // 🔒 Verifica se o usuário já tem 5 empréstimos abertos
        $qtdeEmprestimosAbertos = Borrowing::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        if ($qtdeEmprestimosAbertos >= 5) {
            return redirect()->route('books.show', $book)
                ->with('error', 'Este usuário já possui 5 livros emprestados. Não é possível registrar mais empréstimos.');
        }

        // ✅ Cria o empréstimo
        Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)
            ->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function returnBook(Borrowing $borrowing)
    {
        $borrowing->update([
            'returned_at' => now(),
        ]);

        // ⏱️ Verifica atraso
        $dataEmprestimo = Carbon::parse($borrowing->borrowed_at);
        $dataDevolucao = Carbon::now();
        $diasEmprestado = $dataEmprestimo->diffInDays($dataDevolucao);
        $diasDeAtraso = $diasEmprestado - 15;

        if ($diasDeAtraso > 0) {
            $multa = $diasDeAtraso * 0.50;
            $usuario = $borrowing->user;
            $usuario->debit += $multa;
            $usuario->save();

            return redirect()->route('books.show', $borrowing->book_id)
                ->with('success', 'Devolução registrada com sucesso. Multa de R$ ' . number_format($multa, 2, ',', '.') . ' aplicada por atraso.');
        }

        return redirect()->route('books.show', $borrowing->book_id)
            ->with('success', 'Devolução registrada com sucesso.');
    }

    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }
}
