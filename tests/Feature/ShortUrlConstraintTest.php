<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use Illuminate\Support\Facades\Hash;

class ShortUrlConstraintTest extends TestCase
{
    use RefreshDatabase;

    protected $companyA;
    protected $companyB;
    protected $roleSuperAdmin;
    protected $roleAdmin;
    protected $roleMember;
    protected $superAdmin;
    protected $adminCompanyA;
    protected $memberCompanyA;
    protected $memberTwoCompanyA;
    protected $adminCompanyB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'Company A']);
        $this->companyB = Company::create(['name' => 'Company B']);

        $this->roleSuperAdmin = Role::create(['id' => 1, 'name' => 'SuperAdmin']);
        $this->roleAdmin = Role::create(['id' => 2, 'name' => 'Admin']);
        $this->roleMember = Role::create(['id' => 3, 'name' => 'Member']);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1,
            'company_id' => $this->companyA->id,
        ]);

        $this->adminCompanyA = User::create([
            'name' => 'Admin A',
            'email' => 'admin_a@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2,
            'company_id' => $this->companyA->id,
        ]);

        $this->memberCompanyA = User::create([
            'name' => 'Member A1',
            'email' => 'member_a1@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ]);

        $this->memberTwoCompanyA = User::create([
            'name' => 'Member A2',
            'email' => 'member_a2@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ]);

        $this->adminCompanyB = User::create([
            'name' => 'Admin B',
            'email' => 'admin_b@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2,
            'company_id' => $this->companyB->id,
        ]);
    }

    public function test_admin_and_member_can_create_short_urls()
    {
        // Admin creates short URL
        $adminPayload = ['original_url' => 'https://laravel.com/docs'];
        $adminResponse = $this->actingAs($this->adminCompanyA)->postJson(route('urls.store'), $adminPayload);

        $adminResponse->assertJson([
            'responseCode' => 1,
            'responseText' => 'URL Shortened Successfully',
        ]);

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://laravel.com/docs',
            'company_id' => $this->companyA->id,
            'created_by' => $this->adminCompanyA->id,
        ]);

        // Member creates short URL
        $this->flushSession();
        $memberPayload = ['original_url' => 'https://github.com'];
        $memberResponse = $this->actingAs($this->memberCompanyA)->postJson(route('urls.store'), $memberPayload);

        $memberResponse->assertJson([
            'responseCode' => 1,
            'responseText' => 'URL Shortened Successfully',
        ]);

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://github.com',
            'company_id' => $this->companyA->id,
            'created_by' => $this->memberCompanyA->id,
        ]);
    }

    public function test_superadmin_cannot_create_short_urls()
    {
        $payload = ['original_url' => 'https://laravel.com'];

        $response = $this->actingAs($this->superAdmin)->postJson(route('urls.store'), $payload);

        $response->assertJson([
            'responseCode' => 0,
            'responseText' => 'SuperAdmin cannot create short URLs.',
        ]);
    }

    public function test_admin_can_only_see_the_list_of_all_short_urls_created_in_their_own_company()
    {
        $urlA1 = ShortUrl::create([
            'original_url' => 'https://companya-admin.com',
            'short_code' => 'A1URLS',
            'company_id' => $this->companyA->id,
            'created_by' => $this->adminCompanyA->id,
        ]);

        $urlA2 = ShortUrl::create([
            'original_url' => 'https://companya-member.com',
            'short_code' => 'A2URLS',
            'company_id' => $this->companyA->id,
            'created_by' => $this->memberCompanyA->id,
        ]);

        $urlB = ShortUrl::create([
            'original_url' => 'https://companyb.com',
            'short_code' => 'B1URLS',
            'company_id' => $this->companyB->id,
            'created_by' => $this->adminCompanyB->id,
        ]);

        $response = $this->actingAs($this->adminCompanyA)->get(route('urls.index'));

        $response->assertStatus(200);
        $response->assertViewHas('urls', function ($urls) use ($urlA1, $urlA2, $urlB) {
            return $urls->contains($urlA1) && $urls->contains($urlA2) && !$urls->contains($urlB) && $urls->count() === 2;
        });
    }

    public function test_member_can_only_see_the_list_of_all_short_urls_created_by_themselves()
    {
        $myUrl = ShortUrl::create([
            'original_url' => 'https://mymemberurl.com',
            'short_code' => 'MEMURL',
            'company_id' => $this->companyA->id,
            'created_by' => $this->memberCompanyA->id,
        ]);

        $otherMemberUrl = ShortUrl::create([
            'original_url' => 'https://othermember.com',
            'short_code' => 'OTHMEM',
            'company_id' => $this->companyA->id,
            'created_by' => $this->memberTwoCompanyA->id,
        ]);

        $adminUrl = ShortUrl::create([
            'original_url' => 'https://adminurl.com',
            'short_code' => 'ADMURL',
            'company_id' => $this->companyA->id,
            'created_by' => $this->adminCompanyA->id,
        ]);

        $response = $this->actingAs($this->memberCompanyA)->get(route('urls.index'));

        $response->assertStatus(200);
        $response->assertViewHas('urls', function ($urls) use ($myUrl, $otherMemberUrl, $adminUrl) {
            return $urls->contains($myUrl) && !$urls->contains($otherMemberUrl) && !$urls->contains($adminUrl) && $urls->count() === 1;
        });
    }

    public function test_short_urls_are_publicly_resolvable_and_redirect_to_the_original_url()
    {
        $shortUrl = ShortUrl::create([
            'original_url' => 'https://laravel.com',
            'short_code' => 'LARAVEL',
            'company_id' => $this->companyA->id,
            'created_by' => $this->adminCompanyA->id,
        ]);

        // Public resolution as unauthenticated guest
        $guestResponse = $this->get(route('url.resolve', 'LARAVEL'));
        $guestResponse->assertRedirect('https://laravel.com');

        // Resolution as authenticated user
        $authResponse = $this->actingAs($this->memberCompanyA)->get(route('url.resolve', 'LARAVEL'));
        $authResponse->assertRedirect('https://laravel.com');
    }

    public function test_superadmin_can_see_the_list_of_all_short_urls_for_every_company()
    {
        $urlA = ShortUrl::create([
            'original_url' => 'https://companya.com',
            'short_code' => 'COMPA1',
            'company_id' => $this->companyA->id,
            'created_by' => $this->adminCompanyA->id,
        ]);

        $urlB = ShortUrl::create([
            'original_url' => 'https://companyb.com',
            'short_code' => 'COMPB1',
            'company_id' => $this->companyB->id,
            'created_by' => $this->adminCompanyB->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('urls.index'));

        $response->assertStatus(200);
        $response->assertViewHas('urls', function ($urls) use ($urlA, $urlB) {
            return $urls->contains($urlA) && $urls->contains($urlB) && $urls->count() === 2;
        });
    }
}
