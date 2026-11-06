<x-guest-layout>
    <form method="POST" action="{{ route('my_sql_db_connection') }}">
    @csrf
        @if(session()->has('error'))
            <h1 style="color: darkred">{{ session('error') }}</h1>
        @endif
    <!-- Name -->
        <div>
            <x-input-label for="db_host" :value="__('DB Host')" />
            <x-text-input id="db_host" class="block mt-1 w-full" type="text" name="db_host" :value="old('db_host',env('DB_HOST'))" required autofocus autocomplete="db_host" />
            <x-input-error :messages="$errors->get('db_host')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="db_port" :value="__('DB Port')" />
            <x-text-input id="db_port" class="block mt-1 w-full" type="text" name="db_port" :value="old('db_port',env('DB_PORT'))" required autofocus autocomplete="db_port" />
            <x-input-error :messages="$errors->get('db_port')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="db_name" :value="__('DB Name')" />
            <x-text-input id="db_name" class="block mt-1 w-full" type="text" name="db_name" :value="old('db_name',env('DB_DATABASE'))" required autofocus autocomplete="db_name" />
            <x-input-error :messages="$errors->get('db_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="db_username" :value="__('DB Username')" />
            <x-text-input id="db_username" class="block mt-1 w-full" type="text" name="db_username" :value="old('db_username',env('DB_USERNAME'))" required autocomplete="db_username" />
            <x-input-error :messages="$errors->get('db_username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="db_password" :value="__('DB Password')" />

            <x-text-input id="db_password" class="block mt-1 w-full"
                          type="text" :value="old('db_password',env('DB_PASSWORD'))"
                          name="db_password"
                           autocomplete="new-password" />

            <x-input-error :messages="$errors->get('db_password')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="app_url" :value="__('App Url')" />

            <x-text-input id="app_url" class="block mt-1 w-full"
                          type="text" :value="old('app_url',env('APP_URL'))"
                          name="app_url" />

            <x-input-error :messages="$errors->get('app_url')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="asset_url" :value="__('Asset Url')" />

            <x-text-input id="asset_url" class="block mt-1 w-full"
                          type="text" :value="old('asset_url',env('ASSET_URL'))"
                          name="asset_url" />

            <x-input-error :messages="$errors->get('asset_url')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-4">
                {{ __('Updated') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
