<?php

namespace Tests\Feature;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メッセージ一覧の表示(): void
    {
        // 事前情報としてメッセージ作成
        Message::create([ 'body' => 'Hello World' ]);
        Message::create([ 'body' => 'Hello Laravel' ]);

        // メッセージ一覧にリクエストを送信し、200(OK)が返る
        // メッセージ一覧にメッセージ1、メッセージ2が表示される
        $this->get('messages')
            ->assertOk()
            ->assertSeeInOrder([
                'Hello World',
                'Hello Laravel',
            ]);
    }
}
