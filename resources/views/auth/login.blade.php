<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center text-4xl font-bold text-gray-800 mb-6">
        EventEase
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- メールアドレス -->
        <div>
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- パスワード -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col items-center justify-center mt-4 space-y-4">
            <!-- ログインボタン -->
            <x-primary-button class="w-full">
                {{ __('ログイン') }}
            </x-primary-button>

            <!-- ユーザ登録へ -->
            <a class="underline text-sm text-blue-600 hover:text-blue-400" href="{{ route('register') }}">
            {{ __('ユーザ登録') }}
            </a>
        </div>
    </form>
</x-guest-layout>
