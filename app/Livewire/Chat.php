<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $users;
    public $selectedUser;
    public $newMessage;
    public $groups;
    public $selectedChat;
    public $messages;
    public $authId;
    public $loginID;

    protected $listeners = [];

     public function mount()
    {
        $this->authId = Auth::id();
        $this->loginID = Auth::id();

        // Ambil semua pengguna kecuali pengguna yang sedang login
        $this->users = User::whereNot("id", $this->authId)->latest()->get();
        // Ambil grup di mana pengguna adalah anggota
        $this->groups = Auth::user()->groups()->get();

        // Inisialisasi obrolan yang dipilih (default ke pengguna pertama atau grup pertama jika tidak ada pengguna)
        $this->selectedChat = $this->users->first() ?? $this->groups->first();

        $this->loadMessages();
        $this->updateListeners(); // Panggil ini untuk menginisialisasi listener
    }

    // Metode untuk memperbarui listener setelah mount atau perubahan penting
    protected function updateListeners()
    {
        $this->listeners = [
            "echo-private:chat.{$this->loginID}, MessageSent" => "newChatMessageNotification"
        ];

        // Tambahkan listener untuk setiap grup di mana pengguna adalah anggota
        foreach ($this->groups as $group) {
            $this->listeners["echo-private:group.{$group->id}, MessageSent"] = "newChatMessageNotification";
        }
    }


    public function selectUser($id)
    {
        $this->selectedChat = User::find($id);
        $this->loadMessages();
    }

    public function selectGroup($id) // Metode baru untuk memilih grup
    {
        $group = Group::find($id);
        // Pastikan pengguna adalah anggota grup ini sebelum mengizinkan akses
        if ($group && $group->members->contains($this->authId)) {
            $this->selectedChat = $group;
            $this->loadMessages();
        } else {
            session()->flash('error', 'Anda bukan anggota grup ini.');
            $this->selectedChat = null; // Reset selected chat if unauthorized
            $this->messages = collect();
        }
    }

    public function loadMessages()
    {
        if (!$this->selectedChat) {
            $this->messages = collect();
            return;
        }

        if ($this->selectedChat instanceof User) {
            // Muat pesan personal
            $this->messages = ChatMessage::query()
                ->where(function ($q){
                    $q->where("sender_id", Auth::id())
                        ->where("receiver_id", $this->selectedChat->id);
                })
                ->orWhere(function ($q){
                    $q->where("sender_id", $this->selectedChat->id)
                        ->where("receiver_id", Auth::id());
                })
                ->whereNull('group_id') // Hanya pesan personal
                ->with(['sender']) // Muat relasi pengirim
                ->orderBy('created_at', 'asc')
                ->get();
        } elseif ($this->selectedChat instanceof Group) {
            // Muat pesan grup
            $this->messages = ChatMessage::where('group_id', $this->selectedChat->id)
                ->with(['sender']) // Muat relasi pengirim
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
             $this->messages = collect();
        }
    }

    public function submit()
    {
        if (empty($this->newMessage) || !$this->selectedChat) {
            return;
        }

        $message = null;

        if ($this->selectedChat instanceof User) {
            $message = ChatMessage::create([
                "sender_id" => Auth::id(),
                "receiver_id" => $this->selectedChat->id,
                "message" => $this->newMessage,
            ]);
            broadcast(new MessageSent($message)); // Broadcast ke penerima personal
        } elseif ($this->selectedChat instanceof Group) {
            $message = ChatMessage::create([
                "sender_id" => Auth::id(),
                "group_id" => $this->selectedChat->id,
                "message" => $this->newMessage,
            ]);
            broadcast(new MessageSent($message)); // Broadcast ke channel grup
        }

        if ($message) {
            $this->messages->push($message->load('sender')); // Pastikan pengirim dimuat untuk UI
        }

        $this->newMessage = '';
    }

    public function updateNewMessage($value)
    {
        // Logika indikator mengetik (opsional, bisa lebih kompleks untuk grup)
        if ($this->selectedChat instanceof User) {
            $this->dispatch("userTyping", userID: $this->loginID, userName: Auth::user()->name, selectedUserID: $this->selectedChat->id);
        }
        // Untuk grup, Anda mungkin perlu memancarkan event typing ke channel grup.
        // Penanganan di frontend juga perlu diadaptasi untuk menampilkan beberapa indikator typing.
    }

    // Getter untuk listeners, penting untuk Livewire saat ada perubahan dinamis
    public function getListeners()
    {
        return $this->listeners;
    }

    public function newChatMessageNotification($eventMessage)
    {
        // Ambil objek pesan lengkap dari ID
        $messageObj = ChatMessage::with('sender')->find($eventMessage['id']);

        if (!$messageObj) {
            return; // Pesan tidak ditemukan
        }

        // Jika pesan untuk obrolan yang sedang dipilih, tambahkan ke daftar
        if ($this->selectedChat instanceof User && $eventMessage['sender_id'] == $this->selectedChat->id && empty($eventMessage['group_id'])) {
            $this->messages->push($messageObj);
        } elseif ($this->selectedChat instanceof Group && $eventMessage['group_id'] == $this->selectedChat->id) {
             $this->messages->push($messageObj);
        }
        // Jika pesan untuk obrolan lain (tidak dipilih), Anda dapat menambahkan notifikasi di UI
        // Contoh: unread_messages_count atau badge di samping nama user/grup.
        $this->dispatch('chat-message-received'); // Contoh event untuk notifikasi umum
    }

    public function render()
    {
        return view('livewire.chat');
    }

}
