<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookPostRequest;
use App\Http\Requests\BookPutRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpParser\Node\Expr\Cast\Bool_;

class BookController extends Controller
{
    public function index(): Response
    {
        // 書籍一覧を取得
        $books = Book::with('category')
                    ->orderBy('category_id')
                    ->orderBy('title')
                    ->get();

        // 書籍一覧をレスポンスとして返す
        return response()
                ->view('admin/book/index', ['books' => $books])
                ->header('Content-Type', 'text/html')
                ->header('Content-Encoding', 'UTF-8');
    }

    public function show(Book $book): View
    {
        Log::info('書籍詳細情報が参照されました。ID=' . $book->id);
        
        return view('admin/book/show', compact('book'));
    }

    public function create(): View
    {
        // ビューにカテゴリ一覧を表示するために全件取得
        $categories = Category::all();

        // 著者一覧を表示するために全件取得
        $authors = Author::all();

        // ビューオブジェクトを返す
        return view('admin/book/create',
            compact('categories', 'authors'));

    }

    public function store(BookPostRequest $request): RedirectResponse
    {
        // 書籍データ登録用のオブジェクトを作成する
        $book = new Book();

        // リクエストオブジェクトからパラメータを取得
        $book->category_id = $request->category_id;
        $book->title = $request->title;
        $book->price = $request->price;
        $book->admin_id = Auth::id();

        DB::transaction(function() use($book, $request) {

            // 保存
            $book->save();

            // 著者書籍テーブルを登録
            $book->authors()->attach($request->author_ids);
        });


        // 保存した書籍情報をレスポンスとして返す
        return redirect(route('book.index'))
            ->with('message', $book->title . 'を追加しました。');
    }

    public function edit(Book $book): View
    {
        // カテゴリ一覧を表示するために全件取得
        $categories = Category::all();

        // 著者一覧を表示するために全件取得
        $authors = Author::all();

        // 書籍に紐づく著者IDの一覧を取得
        $authorIds = $book->authors()->pluck('id')->all();

        return view('admin/book/edit',
            compact('book', 'categories', 'authors', 'authorIds'));
    }

    public function update(BookPutRequest $request, Book $book): RedirectResponse
    {
        // リクエストオブジェクトからパラメータを取得する
        $book->category_id = $request->category_id;
        $book->title = $request->title;
        $book->price = $request->price;

        DB::transaction(function() use($book, $request) {
            // 更新
            $book->update();

            // 書籍と著者の関連付けを更新する
            $book->authors()->sync($request->author_ids);
        });

        return redirect(route('book.index'))
            ->with('message', $book->title . 'を変更しました。');
    }

    public function destroy(Book $book): RedirectResponse
    {
        // 削除
        $book->delete();

        return redirect(route('book.index'))
            ->with('message', $book->title . 'を削除しました。');
    }
}
