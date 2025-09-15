            <aside :class="{ 'w-full md:w-64': sidebarOpen, 'w-0 md:w-16 hidden md:block': !sidebarOpen }"
                class="bg-sidebar text-sidebar-foreground border-r border-gray-200 dark:border-gray-700 sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4">
                        <ul class="space-y-1 px-2">
                            <!-- Dashboard -->
                            <x-layouts.sidebar-link href="{{ route('admin.dashboard') }}" icon='fas-house'
                                :active="request()->routeIs('admin.dashboard')">Dashboard</x-layouts.sidebar-link>

                            @if (auth()->check() &&
                                    (auth()->user()->hasPermission('roles.manage') || auth()->user()->hasPermission('permissions.manage')))
                                <div class="mt-4">
                                    <x-layouts.sidebar-two-level-link-parent title="Administration" icon="fas-user-tie"
                                        :active="request()->routeIs('admin.roles.*') ||
                                            request()->routeIs('admin.permissions.*')">
                                        @if (auth()->user()->hasPermission('roles.manage'))
                                            <x-layouts.sidebar-two-level-link href="{{ route('admin.roles.index') }}"
                                                icon='fas-user-shield'
                                                :active="request()->routeIs('admin.roles.*')">Roles</x-layouts.sidebar-two-level-link>
                                        @endif
                                        @if (auth()->user()->hasPermission('permissions.manage'))
                                            <x-layouts.sidebar-two-level-link
                                                href="{{ route('admin.permissions.index') }}" icon='fas-key'
                                                :active="request()->routeIs('admin.permissions.*')">Permissions</x-layouts.sidebar-two-level-link>
                                        @endif
                                    </x-layouts.sidebar-two-level-link-parent>
                                </div>
                            @endif

                            <!-- Example three level -->
                            <x-layouts.sidebar-two-level-link-parent title="Example three level" icon="fas-house"
                                :active="request()->routeIs('three-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                    :active="request()->routeIs('three-level*')">Single Link</x-layouts.sidebar-two-level-link>

                                <x-layouts.sidebar-three-level-parent title="Third Level" icon="fas-house"
                                    :active="request()->routeIs('three-level*')">
                                    <x-layouts.sidebar-three-level-link href="#" :active="request()->routeIs('three-level*')">
                                        Third Level Link
                                    </x-layouts.sidebar-three-level-link>
                                </x-layouts.sidebar-three-level-parent>
                            </x-layouts.sidebar-two-level-link-parent>


                        </ul>
                    </nav>
                </div>
            </aside>
