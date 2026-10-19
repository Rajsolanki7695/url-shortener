<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Edit Short URL') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('Update the destination address for short code') }} <span class="font-mono font-semibold text-indigo-600">{{ $url->short_code }}</span>
                </p>
            </div>
            <a href="{{ route('urls.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:p-8">

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('urls.update', $url->id) }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-label value="{{ __('Short Code') }}" />
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm font-mono">
                                {{ url('/s') }}/
                            </span>
                            <x-input type="text" class="rounded-l-none bg-gray-50 text-gray-600 cursor-not-allowed" :value="$url->short_code" readonly />
                        </div>
                    </div>

                    <div>
                        <x-label for="original_url" value="{{ __('Original Destination URL') }}" />
                        <x-input id="original_url" class="block mt-1 w-full" type="url" name="original_url" :value="old('original_url', $url->original_url)" required autofocus />
                        <x-input-error for="original_url" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('urls.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <x-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Update Short URL') }}
                        </x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
