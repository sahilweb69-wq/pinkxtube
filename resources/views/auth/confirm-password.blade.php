<x-layouts.auth :title="__('Confirm Password')">
    <!-- Confirm Password Card -->
    <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Confirm Password') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Please confirm your password before continuing.') }}
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <!-- Password Input -->
                <div class="mb-4">
                    <label for="password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Password') }}</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('password')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Button -->
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    {{ __('Confirm Password') }}
                </button>
            </form>

            <!-- Forgot Password Link -->
            <div class="text-center mt-6">
                <a href="{{ route('password.request') }}"
                    class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Forgot your password?') }}</a>
            </div>
        </div>
    </div>
</x-layouts.auth>
