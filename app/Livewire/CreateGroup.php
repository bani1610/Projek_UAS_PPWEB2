<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout; // Import ini jika Anda ingin layout khusus

// #[Layout('components.layouts.app')] // Contoh layout untuk halaman ini
class CreateGroup extends Component
{
    public string $name = '';
    public ?string $description = '';

    public $search = '';
    public array $selectedMembers = []; // Untuk memilih anggota

    public $users; // Daftar pengguna yang bisa ditambahkan ke grup

    public function mount()
    {
        // Hanya dosen yang bisa mengakses halaman ini
        if (! Auth::user()->isLecturer()) {
            abort(403, 'Unauthorized action.');
        }
        $this->users = User::where('id', '!=', Auth::id())->get(); // Ambil semua pengguna kecuali diri sendiri
    }


    public function createGroup()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'selectedMembers' => ['array'],
            'selectedMembers.*' => ['exists:users,id'], // Validasi ID anggota yang dipilih
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'lecturer_id' => Auth::id(),
        ]);

        // Tambahkan dosen sebagai anggota pertama secara otomatis
        $group->members()->attach(Auth::id());

        // Tambahkan anggota yang dipilih
        if (!empty($this->selectedMembers)) {
            $group->members()->attach($this->selectedMembers);
        }

        session()->flash('message', 'Grup berhasil dibuat!');
        $this->redirect(route('chat', ['group' => $group->id]), navigate: true); 
    }


}
