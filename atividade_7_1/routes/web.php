<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Certifique-se de que esta linha esteja presente

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rota da página inicial
Route::get('/', function () {
    return view('welcome');
});

// Rotas de Autenticação (Login, Registro, etc.)
Auth::routes();

// Rota para o painel de controle ou home do usuário
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rotas de Recursos (Resource Routes)
// A ordem é importante para evitar que rotas genéricas capturem URLs de rotas específicas.

// Rotas para Categorias (Resource)
Route::resource('categories', CategoryController::class);

// Rotas para Autores (Resource)
// Nota: O padrão Laravel para resource é plural (ex: 'authors'). Manter 'author' como singular é possível, mas o plural é mais comum.
Route::resource('author', AuthorController::class);

// Rotas para Editoras (Resource)
// Nota: O padrão Laravel para resource é plural (ex: 'publishers'). Manter 'publisher' como singular é possível, mas o plural é mais comum.
Route::resource('publisher', PublisherController::class);

// Rotas Customizadas para Criação de Livros (COM ID e COM SELECT)
// Estas devem vir ANTES do 'Route::resource('books')' para que as URLs específicas sejam priorizadas.
Route::get('/books/create-id-number', [BookController::class, 'createWithId'])->name('books.create.id');
Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])->name('books.store.id');

Route::get('/books/create-select', [BookController::class, 'createWithSelect'])->name('books.create.select');
Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])->name('books.store.select');

// Rotas de Recursos para Livros (RESTful - index, show, edit, update, destroy)
// Excluímos 'create' e 'store' aqui porque temos rotas customizadas para eles acima.
Route::resource('books', BookController::class)->except(['create', 'store']);

// Rotas para Usuários (Resource, excluindo create, store, destroy)
Route::resource('users', UserController::class)->except(['create', 'store', 'destroy']);

// Rotas para Empréstimos (Borrowings)
// Rota para registrar um empréstimo de um livro específico
Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])->name('books.borrow');

// Rota para listar o histórico de empréstimos de um usuário
Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])->name('users.borrowings');

// Rota para registrar a devolução de um empréstimo específico
Route::patch('/borrowings/{borrowing}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');