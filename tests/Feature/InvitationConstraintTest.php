<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class InvitationConstraintTest extends TestCase
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
            'name' => 'Admin Company A',
            'email' => 'admin_a@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2,
            'company_id' => $this->companyA->id,
        ]);

        $this->memberCompanyA = User::create([
            'name' => 'Member Company A',
            'email' => 'member_a@test.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ]);
    }

    public function test_superadmin_can_invite_admin_in_a_new_company()
    {
        $payload = [
            'name' => 'New Company Admin',
            'email' => 'newadmin@newcompany.com',
            'role_id' => 2,
            'company_id' => $this->companyB->id,
        ];

        $response = $this->actingAs($this->superAdmin)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 1,
            'responseText' => 'User invited successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@newcompany.com',
            'role_id' => 2,
            'company_id' => $this->companyB->id,
        ]);
    }

    public function test_superadmin_can_invite_admin_creating_a_new_company_on_the_fly()
    {
        $payload = [
            'name' => 'Brand New Admin',
            'email' => 'brandnew@acme.com',
            'role_id' => 2,
            'new_company_name' => 'Acme Corporation',
        ];

        $response = $this->actingAs($this->superAdmin)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 1,
            'responseText' => 'User invited successfully.',
        ]);

        $newCompany = Company::where('name', 'Acme Corporation')->first();
        $this->assertNotNull($newCompany);

        $this->assertDatabaseHas('users', [
            'email' => 'brandnew@acme.com',
            'role_id' => 2,
            'company_id' => $newCompany->id,
        ]);
    }

    public function test_admin_can_invite_another_admin_in_own_company()
    {
        $payload = [
            'name' => 'Colleague Admin',
            'email' => 'colleague_admin@test.com',
            'role_id' => 2,
            'company_id' => $this->companyA->id,
        ];

        $response = $this->actingAs($this->adminCompanyA)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 1,
            'responseText' => 'User invited successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'colleague_admin@test.com',
            'role_id' => 2,
            'company_id' => $this->companyA->id,
        ]);
    }

    public function test_admin_can_invite_member_in_own_company()
    {
        $payload = [
            'name' => 'Colleague Member',
            'email' => 'colleague_member@test.com',
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ];

        $response = $this->actingAs($this->adminCompanyA)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 1,
            'responseText' => 'User invited successfully.',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'colleague_member@test.com',
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ]);
    }

    public function test_admin_cannot_invite_user_to_another_company()
    {
        $payload = [
            'name' => 'External Admin',
            'email' => 'external@test.com',
            'role_id' => 2,
            'company_id' => $this->companyB->id,
        ];

        $response = $this->actingAs($this->adminCompanyA)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 0,
            'responseText' => 'Admin can only invite users to their own company.',
        ]);
    }

    public function test_member_cannot_invite_users()
    {
        $payload = [
            'name' => 'Member Invite Attempt',
            'email' => 'attempt@test.com',
            'role_id' => 3,
            'company_id' => $this->companyA->id,
        ];

        $response = $this->actingAs($this->memberCompanyA)->postJson(route('invite.user'), $payload);

        $response->assertJson([
            'responseCode' => 0,
            'responseText' => 'You do not have privilege to invite users.',
        ]);
    }
}
