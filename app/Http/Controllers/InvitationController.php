<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use App\Helpers\AdminHelper;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function getInvite()
    {
        $currentUser = auth()->user();

        if ((int) $currentUser->role_id === 3) {
            return redirect()->route('dashboard')->with('error', 'You do not have privilege to invite users.');
        }

        if ((int) $currentUser->role_id === 2) {
            $roles = Role::whereIn('id', [2, 3])->get();
            $companies = Company::where('id', $currentUser->company_id)->get();
        } else {
            $roles = Role::all();
            $companies = Company::all();
        }

        return view('admin.invite', compact('roles', 'companies'));
    }

    public function postInviteUser(Request $request)
    {
        $currentUser = auth()->user();

        if ((int) $currentUser->role_id === 3) {
            if ($request->expectsJson()) {
                return CustomHelper::ErrorResponse('You do not have privilege to invite users.');
            }
            return redirect()->route('dashboard')->with('error', 'You do not have privilege to invite users.');
        }

        if ((int) $currentUser->role_id === 1 && !empty($request->new_company_name)) {
            $newCompany = Company::firstOrCreate(['name' => trim($request->new_company_name)]);
            $request->merge(['company_id' => $newCompany->id]);
        }

        $targetRoleId = (int) $request->role_id;
        $targetCompanyId = (int) $request->company_id;

        if ((int) $currentUser->role_id === 2) {
            if (!in_array($targetRoleId, [2, 3])) {
                if ($request->expectsJson()) {
                    return CustomHelper::ErrorResponse('Admin can only invite an Admin or Member in their own company.');
                }
                return redirect()->back()->withInput()->with('error', 'Admin can only invite an Admin or Member in their own company.');
            }

            if ($targetCompanyId !== (int) $currentUser->company_id) {
                if ($request->expectsJson()) {
                    return CustomHelper::ErrorResponse('Admin can only invite users to their own company.');
                }
                return redirect()->back()->withInput()->with('error', 'Admin can only invite users to their own company.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role_id' => 'required|integer|exists:roles,id',
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $password = !empty($request->password) ? $request->password : '12345678';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role_id' => $targetRoleId,
            'company_id' => $targetCompanyId
        ]);

        if ($request->expectsJson()) {
            return CustomHelper::SuccessResponse('User invited successfully.', CustomHelper::CapitalizeArray($user->toArray()));
        }

        return redirect()->route('invite.index')->with('success', 'User invited successfully.');
    }
}
