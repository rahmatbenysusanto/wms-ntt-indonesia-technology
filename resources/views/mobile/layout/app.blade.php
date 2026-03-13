{{-- Mobile App Layout Head --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: {
                    brand: {
                        50:  '#f0fdfd',
                        100: '#ccf7f6',
                        200: '#99eeee',
                        300: '#5ee0df',
                        400: '#39BBBD',
                        500: '#22a3a5',
                        600: '#1a8587',
                        700: '#186a6c',
                        800: '#195558',
                        900: '#194849',
                    },
                },
            },
        },
    }
</script>
{{-- Keep Bootstrap JS for modal support --}}
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
    body { background-color: #f1f5f9; -webkit-tap-highlight-color: transparent; }
    a { text-decoration: none; color: inherit; }
    /* Smooth scroll & overscroll */
    html { scroll-behavior: smooth; overscroll-behavior-y: none; }

    /* Bottom nav safe area */
    .bottom-nav { padding-bottom: env(safe-area-inset-bottom); }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

    /* Card hover ripple */
    .tap-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .tap-card:active { transform: scale(0.98); box-shadow: 0 1px 4px rgba(0,0,0,0.12); }

    /* Status badges */
    .badge-new      { background: #dcfce7; color: #166534; }
    .badge-open     { background: #dbeafe; color: #1e40af; }
    .badge-process  { background: #fef9c3; color: #854d0e; }
    .badge-done     { background: #f1f5f9; color: #475569; }
    .badge-cancel   { background: #fee2e2; color: #991b1b; }
    .badge-inbound  { background: #dcfce7; color: #166534; }
    .badge-outbound { background: #ede9fe; color: #5b21b6; }
    .badge-return   { background: #fef3c7; color: #92400e; }

    /* Input focus nicely */
    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #39BBBD !important;
        box-shadow: 0 0 0 3px rgba(57,187,189,.15) !important;
    }

    /* Modal backdrop */
    .modal-backdrop { backdrop-filter: blur(2px); }

    /* Shimmer loading */
    @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
    .shimmer {
        background: linear-gradient(90deg, #e2e8f0 25%, #f8fafc 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }
</style>
