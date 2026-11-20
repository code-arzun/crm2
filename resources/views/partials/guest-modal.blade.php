<!-- MODAL GUEST (LOGIN / REGISTER) -->
<div x-data="{ 
        showGuestModal: false, 
        tab: 'login' 
    }" 
    x-on:open-guest-modal.window="showGuestModal = true; tab = 'login'"
    x-on:switch-to-register.window="tab = 'register'"
    x-on:switch-to-login.window="tab = 'login'"
    x-show="showGuestModal" 
    x-transition.opacity 
    class="fixed inset-0 z-[100] flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4"
    style="display: none;">
    
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" @click.outside="showGuestModal = false">
        
        <!-- Tombol Close -->
        <div class="absolute top-4 right-4 z-10">
            <button @click="showGuestModal = false" class="text-zinc-400 hover:text-zinc-600 bg-zinc-100 dark:bg-zinc-800 rounded-full p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Tab Navigasi -->
        <div class="flex border-b border-zinc-200 dark:border-zinc-700">
            <button @click="tab = 'login'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'login', 'border-transparent text-zinc-500': tab !== 'login' }" class="flex-1 py-3 text-sm font-bold border-b-2 transition-colors">
                Masuk
            </button>
            <button @click="tab = 'register'" :class="{ 'border-indigo-600 text-indigo-600': tab === 'register', 'border-transparent text-zinc-500': tab !== 'register' }" class="flex-1 py-3 text-sm font-bold border-b-2 transition-colors">
                Daftar
            </button>
        </div>

        <div class="p-6 md:p-8 max-h-[80vh] overflow-y-auto">
            <!-- Komponen Login -->
            <div x-show="tab === 'login'" style="display: none;">
                @livewire('auth.login')
            </div>

            <!-- Komponen Register -->
            <div x-show="tab === 'register'" style="display: none;">
                @livewire('auth.register')
            </div>
        </div>
    </div>
</div>