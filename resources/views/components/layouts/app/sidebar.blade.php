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

                            <!-- Example two level -->
                            <x-layouts.sidebar-two-level-link-parent title="Example two level" icon="fas-house"
                                :active="request()->routeIs('two-level*')">
                                <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                    :active="request()->routeIs('two-level*')">Child</x-layouts.sidebar-two-level-link>
                            </x-layouts.sidebar-two-level-link-parent>

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

                            @if(auth()->check() && auth()->user()->hasPermission('admin.access'))
                            <div class="mt-4">
                                <div class="px-2 text-xs uppercase text-gray-500">Administration</div>
                                <x-layouts.sidebar-link href="{{ route('admin.roles.index') }}" icon='fas-user-shield'
                                    :active="request()->routeIs('admin.roles.*')">Roles</x-layouts.sidebar-link>
                                <x-layouts.sidebar-link href="{{ route('admin.permissions.index') }}" icon='fas-key'
                                    :active="request()->routeIs('admin.permissions.*')">Permissions</x-layouts.sidebar-link>
                            </div>
                            @endif
                        </ul>
                    </nav>
                </div>
            </aside>
