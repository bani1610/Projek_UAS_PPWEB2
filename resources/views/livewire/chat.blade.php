<div class="">
    <div class="text-center py-7 mb-6 w-full shadow-2xl rounded-2xl">
        <flux:heading size="xl" level="1">{{ __('Chat Sapa') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('chat apapun lebih mudah') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex h-[600px] text-sm border rounded-xl shadow overflow-hidden bg-white dark:bg-zinc-800 dark:border-zinc-700">

    {{-- Kolom 1: Daftar Pesan (Menggabungkan Grup dan User dari kode Anda) --}}
    <aside class="w-full md:w-[320px] bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-700 flex flex-col">
        {{-- Header Daftar Pesan --}}
        <div class="p-4 border-b border-slate-200 dark:border-zinc-700">
            <h1 class="text-lg font-bold text-slate-800 dark:text-white">Chats</h1>
        </div>

        {{-- Daftar Chat (Groups & Direct Messages) --}}
        <div class="flex-1 overflow-y-auto">
            
            {{-- Bagian Groups dari kode Anda --}}
            <h3 class="p-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase">Groups</h3>
            @forelse ($groups as $group)
                <div wire:click="selectGroup({{ $group->id }})" class="p-3 flex gap-3 cursor-pointer transition-colors
                    {{ $selectedChat instanceof \App\Models\Group && $selectedChat->id === $group->id ? 'bg-indigo-500 text-white' : 'hover:bg-slate-50 dark:hover:bg-zinc-800' }}">
                    
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center font-bold text-white shrink-0">
                        {{ substr($group->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm">{{ $group->name }}</h3>
                        <p class="text-xs truncate {{ $selectedChat instanceof \App\Models\Group && $selectedChat->id === $group->id ? 'text-indigo-100' : 'text-slate-500 dark:text-zinc-400' }}">
                            {{ $group->lecturer->name }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-3 text-slate-500 dark:text-zinc-400 text-xs">No groups available.</div>
            @endforelse

            {{-- Bagian Direct Messages dari kode Anda --}}
            <h3 class="p-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase border-t border-slate-200 dark:border-zinc-700">Direct Messages</h3>
            @forelse ($users as $user)
                 <div wire:click="selectUser({{ $user->id }})" class="p-3 flex gap-3 cursor-pointer transition-colors
                    {{ $selectedChat instanceof \App\Models\User && $selectedChat->id === $user->id ? 'bg-indigo-500 text-white' : 'hover:bg-slate-50 dark:hover:bg-zinc-800' }}">
                     
                     <img src="https://i.pravatar.cc/150?u={{ $user->email }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full shrink-0">
                     <div class="flex-1">
                        <h3 class="font-bold text-sm">{{ $user->name}}</h3>
                        <p class="text-xs truncate {{ $selectedChat instanceof \App\Models\User && $selectedChat->id === $user->id ? 'text-indigo-100' : 'text-slate-500 dark:text-zinc-400' }}">
                            {{ $user->email}}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-3 text-slate-500 dark:text-zinc-400 text-xs">No other users available.</div>
            @endforelse
        </div>
    </aside>

    {{-- Kolom 2: Jendela Chat Utama --}}
    @if ($selectedChat)
        <main class="flex-1 flex flex-col">
            {{-- Header Chat --}}
            <header class="p-3 flex items-center gap-3 bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-700">
                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center font-bold text-white shrink-0">
                    {{ substr($selectedChat->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="font-bold text-base dark:text-white">{{ $selectedChat->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        @if ($selectedChat instanceof \App\Models\Group)
                            Created by: {{ $selectedChat->lecturer->name }}
                        @else
                            {{ $selectedChat->email }}
                        @endif
                    </p>
                </div>
            </header>

            {{-- Area Pesan --}}
            <div class="flex-1 p-4 space-y-4 overflow-y-auto bg-slate-50 dark:bg-zinc-800" x-data="{
                scrollMessages: () => {
                    const messagesContainer = document.querySelector('.flex-1.p-4');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }
            }" x-init="scrollMessages()" x-on:livewire:updated="scrollMessages()">
                
                @forelse ($messages as $message)
                    {{-- Pesan keluar (milik Anda) --}}
                    @if($message->sender_id === auth()->id())
                        <div class="flex items-end gap-2 justify-end">
                            <div class="bg-blue-600 text-white p-3 rounded-lg rounded-br-none max-w-sm">
                                <p>{{ $message->message }}</p>
                            </div>
                        </div>
                    @else
                    {{-- Pesan masuk --}}
                        <div class="flex items-end gap-2 justify-start">
                            <img src="https://i.pravatar.cc/150?u={{ $message->sender->email }}" alt="{{ $message->sender->name }}" class="w-8 h-8 rounded-full">
                            <div class="bg-gray-200 dark:bg-zinc-700 dark:text-white p-3 rounded-lg rounded-bl-none max-w-sm">
                                @if ($selectedChat instanceof \App\Models\Group)
                                    <small class="font-semibold text-gray-600 dark:text-zinc-300">{{ $message->sender->name }}</small>
                                @endif
                                <p>{{ $message->message }}</p>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center text-slate-500 dark:text-zinc-400 mt-4">Start a conversation!</div>
                @endforelse
            </div>
            
            {{-- Form Input Pesan --}}
            <div class="p-4 bg-white dark:bg-zinc-900 border-t border-slate-200 dark:border-zinc-700">
                <form wire:submit.prevent="submit" class="flex items-center gap-2">
                    <input
                        wire:model="newMessage"
                        type="text"
                        placeholder="Type your message..."
                        class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300 dark:bg-zinc-800 dark:border-zinc-600 dark:text-white"
                        autocomplete="off"
                    />
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">
                        Send
                    </button>
                </form>
            </div>
        </main>
    @else
        {{-- Tampilan saat tidak ada chat yang dipilih --}}
        <div class="flex-1 flex items-center justify-center bg-slate-50 dark:bg-zinc-800">
            <div class="text-center">
                <h2 class="text-xl font-bold text-slate-700 dark:text-zinc-300">Select a chat</h2>
                <p class="text-slate-500 dark:text-zinc-400">Choose a user or group to start messaging.</p>
            </div>
        </div>
    @endif
    </div>

</div>

<script>
    document.addEventListener('livewire:initialized', () => {
      Livewire.on('usertyping', (event) => {
        console.log(event) // Anda bisa mengimplementasikan indikator typing di sini
      });
      Livewire.on('chat-message-received', () => {
         // Opsional: play sound atau tampilkan notifikasi pop-up
         // console.log('New message received!');
      });
    });
</script>
