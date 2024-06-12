<?php

namespace Tests\Feature\Auth\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン失敗()
    {
        // 事前情報としてログイン用ユーザー作成
        $admin = Admin::factory()->create([
            'login_id' => 'hoge',
            'password' => Hash::make('hogehoge'),
        ]);

        // IDが一致しない場合
        $this->from(route('admin.store'))
            ->post(route('admin.store'), [
                'login_id' => 'fuga',
                'password' => 'hogehoge',
            ])
            ->assertRedirect(route('admin.create'));

        // パスワードが一致しない場合
        $this->from(route('admin.store'))
            ->post(route('admin.store'), [
                'login_id' => 'hoge',
                'password' => 'fugafuga',
            ])
            ->assertRedirect(route('admin.create'));

        // 認証されてない
        $this->assertGuest('admin');
    }
}
