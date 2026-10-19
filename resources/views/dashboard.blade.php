<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500">{{ __('Current Role') }}</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ auth()->user()->role->name ?? 'Role #' . auth()->user()->role_id }}</div>
                    <div class="mt-1 text-xs text-gray-500">
                        @if((int) auth()->user()->role_id === 1)
                            {{ __('URL creation restricted for SuperAdmin') }}
                        @else
                            {{ __('Authorized to generate short URLs') }}
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                    <div class="text-sm font-medium text-gray-500">{{ __('Assigned Company') }}</div>
                    <div class="mt-2 text-2xl font-bold text-gray-900">{{ auth()->user()->company->name ?? 'Company #' . auth()->user()->company_id }}</div>
                    <div class="mt-1 text-xs text-gray-500">{{ __('Single-tenant company scope') }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500 flex flex-col justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500">{{ __('URL Management') }}</div>
                        <div class="mt-2 text-xs text-gray-600">{{ __('View and manage redirect links based on role permissions.') }}</div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('urls.index') }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            {{ __('View URLs') }}
                        </a>
                        @if((int) auth()->user()->role_id !== 1)
                            <a href="{{ route('urls.add') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                {{ __('+ New URL') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500 flex flex-col justify-between">
                    <div>
                        <div class="text-sm font-medium text-gray-500">{{ __('User Invitations') }}</div>
                        <div class="mt-2 text-xs text-gray-600">{{ __('Invite new members with roles and company assignments.') }}</div>
                    </div>
                    <div class="mt-4">
                        @if(in_array(auth()->user()->role_id, [1, 2]))
                            <a href="{{ route('invite.index') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 transition">
                                {{ __('Invite User') }}
                            </a>
                        @else
                            <span class="inline-flex items-center text-xs text-gray-400">
                                {{ __('Admin access required') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>
