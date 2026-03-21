<?php

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('the sign-in screen renders', function () {
    $this->get('/login')->assertOk()->assertSee('Sign in');
});

test('customers sign in and land on their account', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('account.dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('admins sign in and land on the admin dashboard', function () {
    $admin = admin();

    $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
        ->assertRedirect(route('admin.dashboard', absolute: false));
});

test('a wrong password is rejected', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('sign-in is rate limited after five failures', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
    }

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('customers sign out', function () {
    $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');

    $this->assertGuest();
});

test('new customers register', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('account.dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(User::firstWhere('email', 'test@example.com')->is_admin)->toBeFalse();
});

test('registration cannot make someone an admin', function () {
    $this->post('/register', [
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'is_admin' => 1,
    ]);

    expect(User::firstWhere('email', 'sneaky@example.com')->isAdmin())->toBeFalse();
});

test('a guest cart is merged into the account on sign in', function () {
    $user = User::factory()->create();
    $product = product(['stock' => 5]);
    Cart::create(['user_id' => $user->id, 'bmuk_no' => $product->bmuk_no, 'quantity' => 2]);

    $this->post(route('cart.add'), ['bmuk_no' => $product->bmuk_no, 'quantity' => 2]);
    $this->post('/login', ['email' => $user->email, 'password' => 'password']);

    expect(Cart::where('user_id', $user->id)->sole()->quantity)->toBe(4);
});

test('the reset link is emailed and resets the password', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->get('/reset-password/'.$notification->token)->assertOk();

        $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors();

        return true;
    });
});

test('email verification links verify the account', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    $this->actingAs($user)->get($url)->assertRedirect(route('account.dashboard', absolute: false).'?verified=1');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('the verification email can be resent', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post(route('verification.send'));

    Notification::assertSentTo($user, VerifyEmail::class);
});
