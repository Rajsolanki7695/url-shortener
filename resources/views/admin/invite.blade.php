<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Invite User') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Send an invitation to create a new user account with assigned company and role.') }}
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition ease-in-out duration-150">
                {{ __('Back to Dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-md bg-green-50 border border-green-200">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-md bg-red-50 border border-red-200">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="p-4 rounded-md bg-blue-50 border border-blue-200">
                <div class="flex">
                    <div class="shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-semibold text-blue-800">{{ __('Invitation Authorization Policies') }}</h4>
                        <ul class="mt-1 list-disc list-inside text-xs text-blue-700 space-y-1">
                            <li>{{ __('SuperAdmin can invite an Admin in a new company.') }}</li>
                            <li>{{ __('An Admin can invite another Admin or Member in their own company.') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:p-8">
                <form method="POST" action="{{ route('invite.user') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label for="name" value="{{ __('Full Name') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus placeholder="John Doe" />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="email" value="{{ __('Email Address') }}" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required placeholder="john@company.com" />
                        <x-input-error for="email" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="password" value="{{ __('Password (Optional, default: 12345678)') }}" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password"
                            :value="old('password')" placeholder="12345678" />
                        <x-input-error for="password" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="role_id" value="{{ __('Assign Role') }}" />
                        <select id="role_id" name="role_id"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required>
                            <option value="">{{ __('-- Select Role --') }}</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="role_id" class="mt-2" />
                    </div>

                    @if(auth()->user()->role_id === 2)
                        <div>
                            <x-label for="company_name" value="{{ __('Assigned Company (Your Company)') }}" />
                            <x-input id="company_name" class="block mt-1 w-full bg-gray-100 cursor-not-allowed" type="text"
                                :value="auth()->user()->company->name ?? 'Company #' . auth()->user()->company_id" readonly />
                            <input type="hidden" name="company_id" value="{{ auth()->user()->company_id }}">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Admins can invite users to their own company.') }}</p>
                            <x-input-error for="company_id" class="mt-2" />
                        </div>
                    @else
                        <div>
                            <x-label for="company_id" value="{{ __('Assign Existing Company') }}" />
                            <select id="company_id" name="company_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('-- Select Existing Company --') }}</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }} (ID: {{ $company->id }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="company_id" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="new_company_name" value="{{ __('Or Create In A New Company') }}" />
                            <x-input id="new_company_name" class="block mt-1 w-full" type="text" name="new_company_name"
                                :value="old('new_company_name')" placeholder="Enter new company name (e.g. Acme Corp)" />
                            <p class="text-xs text-gray-500 mt-1">{{ __('SuperAdmin can specify a new company name to automatically create it.') }}</p>
                            <x-input-error for="new_company_name" class="mt-2" />
                        </div>
                    @endif

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <x-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Send Invitation') }}
                        </x-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
