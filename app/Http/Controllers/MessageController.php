<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(): View
    {
        // メッセージテーブルのレコードを全件取得
        $messages = Message::orderBy('id')->get();
        
        // messagesというキーで、ビューへ帰す
        return view('message/index', ['messages' => $messages]);
    }

    public function store(Request $request): RedirectResponse
    {
        // リクエストからボディを取得し、保存
        $message = new Message();
        $message->body = $request->body;
        $message->save();

        // 処理後、リダイレクト
        return redirect('/messages');
    }

    public function destroy(string $id): RedirectResponse
    {
        // 削除処理
        DB::delete('delete from messages where id = ?' . [$id]);

        return redirect('/messages');
    }
}