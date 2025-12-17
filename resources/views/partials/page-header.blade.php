<!-- Page Header -->
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 md:mb-8 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2">@yield('page-title', 'Page Title')</h1>
        <p class="text-sm sm:text-base text-gray-600">@yield('page-description', 'Page description')</p>
    </div>
    @hasSection('header-actions')
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
            @yield('header-actions')
        </div>
    @endif
</div>

