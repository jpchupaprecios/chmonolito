@if($errors->any())
    <div class="relative w-full rounded-lg border p-4 mb-4 text-destructive border-destructive/50 dark:border-destructive"
         role="alert">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="24" height="24"
             viewBox="0 0 24 24"
             fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round"
             class="lucide lucide-circle-alert h-4 w-4 absolute left-4 top-4 text-destructive"
        >
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" x2="12" y1="8" y2="12"></line>
            <line x1="12" x2="12.01" y1="16" y2="16"></line>
        </svg>
        <h5 class="pl-8 mb-1 font-medium leading-none tracking-tight text-red-500 text-sm mt-1">Error</h5>
        <div class="text-sm">
            @foreach($errors->all() as $error)
                <p class="leading-relaxed text-red-500 text-sm mt-1">{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif
