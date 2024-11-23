<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px"
    data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <div class="px-6 app-sidebar-logo" id="kt_app_sidebar_logo">
        <a href="{{ route('dashboard.index') }}">
            <img alt="Logo" src="{{ asset('assets/media/logo/default-dark.svg') }}"
                class="h-25px app-sidebar-logo-default" />
            <img alt="Logo" src="{{ asset('assets/media/logo/default-small.svg') }}"
                class="h-20px app-sidebar-logo-minimize" />
        </a>
        <div id="kt_app_sidebar_toggle"
            class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="app-sidebar-minimize">
            <i class="rotate-180 ki-duotone ki-double-left fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
    </div>

    <div class="overflow-hidden app-sidebar-menu flex-column-fluid">
        <div id="kt_app_sidebar_menu_wrapper" class="my-5 app-sidebar-wrapper hover-scroll-overlay-y"
            data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
            data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
            <div class="px-3 menu menu-column menu-rounded menu-sub-indention" id="#kt_app_sidebar_menu"
                data-kt-menu="true" data-kt-menu-expand="false">
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('dashboard.index') }}">
                        <span class="menu-icon">
                            <x-atoms.icon icon="element-11" path="4" />
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                @can('read user')
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('users.index') }}">
                            <span class="menu-icon">
                                <x-atoms.icon icon="user" path="2" />
                            </span>
                            <span class="menu-title">User</span>
                        </a>
                    </div>
                @endcan

                @can('read employee')
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('employees.index') }}">
                            <span class="menu-icon">
                                <x-atoms.icon icon="profile-user" path="4" />
                            </span>
                            <span class="menu-title">Employee</span>
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
