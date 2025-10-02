@extends('layouts.master-without-nav')
@section('title')
    Session Expired
@endsection
@section('content')
<body class="flex items-center justify-center min-h-screen px-4 py-16 bg-cover bg-auth-pattern dark:bg-auth-pattern-dark dark:text-zink-100 font-public">
    <div class="mb-0 border-none shadow-none xl:w-2/3 card bg-white/70 dark:bg-zink-500/70">
        <div class="grid grid-cols-1 gap-0 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="!px-12 !py-12 card-body">
                    <div class="text-center">
                        <div class="mb-4">
                            <i data-lucide="clock" class="w-16 h-16 mx-auto text-yellow-500"></i>
                        </div>
                        <h4 class="mb-2 text-yellow-500 dark:text-yellow-500">Session Expired</h4>
                        <p class="text-slate-500 dark:text-zink-200 mb-6">Your session has expired for security reasons. Please login again to continue.</p>
                        
                        <div class="space-y-3">
                            <a href="{{ route('login') }}" class="w-full text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                Login Again
                            </a>
                            <a href="{{ url('/') }}" class="w-full text-slate-500 btn bg-slate-100 border-slate-100 hover:text-slate-600 hover:bg-slate-200 hover:border-slate-200 focus:text-slate-600 focus:bg-slate-200 focus:border-slate-200 dark:bg-zink-600 dark:text-zink-200 dark:border-zink-600 dark:hover:bg-zink-500 dark:hover:text-zink-100">
                                Go to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mx-2 mt-2 mb-2 border-none shadow-none lg:col-span-7 card bg-white/60 dark:bg-zink-500/60">
                <div class="!px-10 !pt-10 h-full !pb-0 card-body flex flex-col">
                    <div class="flex items-center justify-between gap-3">
                        <div class="grow">
                            <a href="{{ url('index') }}">
                                <x-application-logo />
                            </a>
                        </div>
                        <div class="shrink-0">
                            <x-language />
                        </div>
                    </div>
                    <div class="mt-auto">
                        <img src="{{ URL::asset('build/images/auth/img-01.png') }}" alt="" class="md:max-w-[32rem] mx-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto redirect to login after 5 seconds
        setTimeout(function() {
            window.location.href = '{{ route('login') }}';
        }, 5000);
        
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
@endsection
