<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PassportFeaturesTest extends TestCase
{

    use RefreshDatabase;

    protected $sitePasswordGrantClient;
    protected $sitePersonalAccessClient;

    // https://alexbilbie.com/guide-to-oauth-2-grants/

    public function setUp()
    {
        parent::setUp();

        // Not sure if this is a better way.
//        $this->artisan('passport:client', ['--password' => true]);
        $this->artisan('passport:install');

//        $this->passwordGrantClientId = DB::table('oauth_clients')
//            ->where('name', sprintf('%s Password Grant Client', env('APP_NAME')))
//            ->value('id');



        $this->sitePasswordGrantClient = DB::table('oauth_clients')
            ->select('id', 'secret')
            ->where('name', sprintf('%s Password Grant Client', config('app.name')))
            ->first();

        $this->sitePersonalAccessClient = DB::table('oauth_clients')
            ->select('id', 'secret')
            ->where('name', sprintf('%s Personal Access Client', config('app.name')))
            ->first();

    }

    /**
     *
     */
    public function test_registered_user_can_request_token_from_password_grant_client()
    {
        $user = factory(User::class)->create();

        $response = $this->json('post', '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->sitePasswordGrantClient->id,
            'client_secret' => $this->sitePasswordGrantClient->secret,
            'username' => $user->email,
            'password' => 'secret', // See UserFactory
            'scope' => '*', // scope turns out to be optional but * means grant all scopes
        ]);

//        $response->dump();

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);

    }

    public function test_signed_in_user_can_create_oauth_client() {

        $user = factory(User::class)->create();

        // either 1 can work for this. You need to be signed in to do this
        $this->actingAs($user);
//        Passport::actingAs($user);

        // Need to create clients for her first
        $response = $this->json('post', '/oauth/clients', [
            'name' => "{$user->name} Client",
            'redirect' => 'http://localhost',
        ]);

        $response->assertJsonStructure([
                'id',
                'user_id',
                'name',
                'secret',
                'redirect',
                'personal_access_client',
                'password_client',
                'revoked'
        ]);

        $response->assertJsonFragment([
            'user_id' => $user->id
        ]);


    }

    public function test_signed_in_user_can_get_all_her_oauth_clients() {

        $user = factory(User::class)->create();

        // either 1 can work for this. You need to be signed in to do this
        $this->actingAs($user);
//        Passport::actingAs($user);

        // Need to create clients for her first
        $this->json('post', '/oauth/clients', [
            'name' => "{$user->name} Client",
            'redirect' => 'http://localhost'
        ]);

        $this->json('post', '/oauth/clients', [
            'name' => "{$user->name} Client 2",
            'redirect' => 'http://localhost'
        ]);



        $getUserClientsResponse = $this->json('get', '/oauth/clients');

//        $getUserClientsResponse->dump();

        $getUserClientsResponse->assertJsonStructure([
            [
                'id',
                'user_id',
                'name',
                'secret',
                'redirect',
                'personal_access_client',
                'password_client',
                'revoked'
            ]
        ]);

        $getUserClientsResponse->assertJson([
            [
                'user_id' => $user->id
            ]
        ]);


    }

    public function test_user_can_request_implicit_grant_token() {

        $user = factory(User::class)->create();

        $createdClientResponse = $this->log_in_and_create_client_for_user($user);


        $createdClient = json_decode($createdClientResponse->content());

        $query = http_build_query([
            'client_id' => $createdClient->id,
            'redirect_uri' => $createdClient->redirect,
            'response_type' => 'token',
            'scope' => '',
        ]);

//        dd($query);

        // you need to be logged in when you make this request too
        $response = $this->json('get', '/oauth/authorize?' . $query);

        // If everything goes right, it returns a web page telling you something like
        // ... is requesting permission to access your account.

        $response->assertSee(' is requesting permission to access your account.');

    }

    public function test_user_can_request_authorization_code_grant_token() {

        $user = factory(User::class)->create();

        $createdClientResponse = $this->log_in_and_create_client_for_user($user);


        $createdClient = json_decode($createdClientResponse->content());

        $query = http_build_query([
            'client_id' => $createdClient->id,
            'redirect_uri' => $createdClient->redirect,
            'response_type' => 'code',
            'scope' => '*',
        ]);

//        dd($query);

        // you need to be logged in when you make this request too
        $response = $this->json('get', '/oauth/authorize?' . $query);

        // If everything goes right, it returns a web page telling you something like
        // ... is requesting permission to access your account.

//        $response->dump();
        $response->assertSee(' is requesting permission to access your account.');


    }

    public function test_machine_user_can_perform_client_credentials_grant() {
        // machine to machine typically

        $user = factory(User::class)->create();

        $createdClientResponse = $this->log_in_and_create_client_for_user($user);

        // so you can logout for client credentials grant!
        auth()->logout();

        $response = $this->json('post', '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $createdClientResponse->json('id'),
            'client_secret' => $createdClientResponse->json('secret'),
            'scope' => '*',
        ])->assertJson([
            'token_type' => 'Bearer',
        ])->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
        ]);

    }

    public function test_user_can_request_refresh_token_grant() {

        $this->markTestSkipped();

        $user = factory(User::class)->create();

        $createdClientResponse = $this->log_in_and_create_client_for_user($user);

        $createdClient = json_decode($createdClientResponse->content());

//        dd($createdClient);

        // you need to be logged in when you make this request too
        $response = $this->json('post', '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => 'the-refresh-token', // need to fill this in
            'client_id' => $createdClient->id,
            'client_secret' => $createdClient->secret,
            'scope' => '*',
        ]);

        $response->dump();
    }

    private function log_in_and_create_client_for_user(User $user) : TestResponse {

        $this->actingAs($user);

        return $this->json('post', '/oauth/clients', [
            'name' => "{$user->name} Client",
            'redirect' => 'http://localhost'
        ]);
    }


}
