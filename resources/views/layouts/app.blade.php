<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'My App')</title>
    @stack('head')
</head>
<body class="antialiased">
@yield('content')

{{-- Tracker: loaded once for every page using this layout --}}
<script
    src="{{ asset('tracker.min.js') }}"
    data-api="{{ url('/api/track') }}"
    defer>
</script>

@stack('scripts')
</body>
</html>
