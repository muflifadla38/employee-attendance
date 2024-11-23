<div id="kt_app_footer" class="app-footer">
    <div class="py-3 app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack">
        <div class="order-2 text-dark order-md-1">
            <span class="text-muted fw-semibold me-1">{{ date('Y') }}&copy;</span>
            <a href="{{ route('dashboard.index') }}" target="_blank"
                class="text-gray-800 text-hover-primary">{{ cache('setting:general:name') }}</a>
        </div>
    </div>
</div>
