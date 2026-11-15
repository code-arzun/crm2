@if(session()->has('success') || session()->has('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)" 
         x-transition.opacity.duration.300ms
         class="mb-6 border px-4 py-3 rounded-xl flex items-center justify-between shadow-sm
                {{ session()->has('success') ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' }}">
        
        <div class="flex items-center gap-3">
            <!-- Ikon Sukses -->
            @if(session()->has('success'))
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <!-- Ikon Error -->
            @else
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            @endif

            <span class="font-medium text-sm">{{ session('success') ?? session('error') }}</span>
        </div>

        <!-- Tombol Close -->
        <button @click="show = false" class="text-zinc-400 hover:text-zinc-600 focus:outline-none ml-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif