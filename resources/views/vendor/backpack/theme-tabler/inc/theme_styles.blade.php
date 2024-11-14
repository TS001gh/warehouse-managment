{{-- theme_styles.blade.php --}}
<script>
    document.documentElement.setAttribute("data-bs-theme", localStorage.colorMode ?? 'light');
</script>

@basset('https://unpkg.com/@tabler/core@1.0.0-beta19/dist/css/tabler.min.css')
@basset(base_path('vendor/backpack/theme-tabler/resources/assets/css/style.css'))

<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
