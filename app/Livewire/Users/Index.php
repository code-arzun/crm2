<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = 'semua';
    public $filterStatus = 'semua';

    // Modal Properti
    public $isModalOpen = false;
    public $userId, $name, $email, $telepon, $password, $role, $join_date;

    public function mount()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Hanya Superadmin yang dapat mengakses halaman ini.');
        }
    }

    public function updatingSearch() { $this->resetPage(); }

    public function create()
    {
        $this->resetFields();
        $this->join_date = now()->format('Y-m-d'); // Default hari ini
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->telepon = $user->telepon;
        $this->role = $user->roles->first()?->name;
        $this->join_date = $user->join_date ? $user->join_date->format('Y-m-d') : null;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'telepon' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name',
            'join_date' => 'required|date',
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'telepon' => $this->telepon,
            'join_date' => $this->join_date,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);
        
        // Sinkronisasi Role (hanya 1 role per staf)
        $user->syncRoles([$this->role]);

        $this->dispatch('notify', message: $this->userId ? 'User berhasil diupdate.' : 'User baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: 'Anda tidak bisa menonaktifkan diri sendiri.');
            return;
        }

        if ($user->is_active) {
            // Jika menonaktifkan, catat tanggal resign hari ini
            $user->update([
                'is_active' => false,
                'resign_date' => now()->format('Y-m-d'),
            ]);
            $this->dispatch('notify', message: 'User berhasil dinonaktifkan.');
        } else {
            // Jika mengaktifkan kembali, hapus tanggal resign
            $user->update([
                'is_active' => true,
                'resign_date' => null,
            ]);
            $this->dispatch('notify', message: 'User berhasil diaktifkan kembali.');
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->telepon = '';
        $this->password = '';
        $this->role = '';
        $this->join_date = '';
    }

    public function render()
    {
        // Hanya tampilkan staf internal (bukan customer)
        $query = User::query()->with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'customer'));

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus !== 'semua') {
            $query->where('is_active', $this->filterStatus === 'aktif');
        }

        $users = $query->latest()->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
            'roles' => Role::where('name', '!=', 'customer')->get()
        ])->layout('layouts.app');
    }
}