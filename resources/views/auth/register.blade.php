<x-guest-layout>
    <div class="text-center text-4xl font-bold text-gray-800 mb-6">
        EventEase
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- ユーザ名 -->
        <div>
            <x-input-label for="name" :value="__('ユーザ名')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- メールアドレス -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('メールアドレス')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- パスワード -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('パスワード')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- パスワード確認 -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('パスワード確認')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col items-center justify-center mt-4 space-y-4">
            <!-- ユーザ登録ボタン -->
            <x-primary-button class="w-full">
                {{ __('ユーザ登録') }}
            </x-primary-button>

            <!-- ログインへ -->
            <a class="underline text-sm text-blue-600 hover:text-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('登録済みの方はこちら') }}
            </a>
        </div>
    </form>
</x-guest-layout>
