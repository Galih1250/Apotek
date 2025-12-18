<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Account Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Account Profile Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Account Information</h3>

                        <div class="space-y-2">
                            <p>
                                <span class="font-medium">Name:</span>
                                {{ auth()->user()->name }}
                            </p>
                            <p>
                                <span class="font-medium">Email:</span>
                                {{ auth()->user()->email }}
                            </p>
                            <p>
                                <span class="font-medium">Role:</span>
                                {{ ucfirst(auth()->user()->role ?? 'user') }}
                            </p>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('profile.edit') }}"
                               class="px-4 py-2 bg-gray-700 text-white rounded-lg text-center hover:bg-gray-800 transition">
                                Edit Profile
                            </a>

                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.index') }}"
                                   class="px-4 py-2 bg-red-600 text-white rounded-lg text-center hover:bg-red-700 transition">
                                    Admin Panel
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
