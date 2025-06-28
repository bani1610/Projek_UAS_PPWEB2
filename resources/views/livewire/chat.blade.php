<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden bg-white dark:bg-zinc-800 dark:border-zinc-700">
        <div class="w-1/4 border-r bg-gray-50 dark:bg-zinc-900 dark:border-zinc-700">
            <div class="p-4 font-bold text-gray-700 border-b dark:text-white dark:border-zinc-700">Chats</div>
            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                <h3 class="p-3 text-sm font-semibold text-gray-600 dark:text-zinc-400">Direct Messages</h3>
                @forelse ($users as $user)
                    <div wire:click="selectUser({{ $user->id }})" class="p-3 cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-900/20 transition
                        {{ $selectedChat instanceof \App\Models\User && $selectedChat->id === $user->id ? 'bg-blue-50 dark:bg-blue-900/50 font-semibold' : ''}}">
                        <div class="text-gray-800 dark:text-white">{{ $user->name}}</div>
                        <div class="text-xs text-gray-500 dark:text-zinc-300">{{ $user->email}}</div>
                    </div>
                @empty
                    <div class="p-3 text-gray-500 dark:text-zinc-400">No other users available.</div>
                @endforelse

                <h3 class="p-3 text-sm font-semibold text-gray-600 border-t mt-4 dark:text-zinc-400 dark:border-zinc-700">Groups</h3>
                @forelse ($groups as $group)
                    <div wire:click="selectGroup({{ $group->id }})" class="p-3 cursor-pointer hover:bg-green-100 dark:hover:bg-green-900/20 transition
                        {{ $selectedChat instanceof \App\Models\Group && $selectedChat->id === $group->id ? 'bg-green-50 dark:bg-green-900/50 font-semibold' : ''}}">
                        <div class="text-gray-800 dark:text-white">{{ $group->name}}</div>
                        <div class="text-xs text-gray-500 dark:text-zinc-300">Lecturer: {{ $group->lecturer->name }}</div>
                    </div>
                @empty
                    <div class="p-3 text-gray-500 dark:text-zinc-400">No groups available.</div>
                @endforelse
            </div>
        </div>

        <div class="w-3/4 flex flex-col">
            @if ($selectedChat)
                <div class="p-4 border-b bg-gray-50 dark:bg-zinc-900 dark:border-zinc-700">
                    <div class="text-lg font-semibold text-gray-800 dark:text-white">
                        @if ($selectedChat instanceof \App\Models\User)
                            {{ $selectedChat->name }}
                        @elseif ($selectedChat instanceof \App\Models\Group)
                            {{ $selectedChat->name }} (Group)
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 dark:text-zinc-300">
                        @if ($selectedChat instanceof \App\Models\User)
                            {{ $selectedChat->email }}
                        @elseif ($selectedChat instanceof \App\Models\Group)
                            Created by: {{ $selectedChat->lecturer->name }}
                        @endif
                    </div>
                </div>
            @else
                <div class="p-4 border-b bg-gray-50 dark:bg-zinc-900 dark:border-zinc-700">
                    <div class="text-lg font-semibold text-gray-800 dark:text-white">Select a chat</div>
                    <div class="text-xs text-gray-500 dark:text-zinc-300">Choose a user or group from the left sidebar to start messaging.</div>
                </div>
            @endif

            <div class="flex-1 p-4 overflow-y-auto space-y-2 bg-gray-50 dark:bg-zinc-800" x-data="{
                scrollMessages: () => {
                    const messagesContainer = document.querySelector('.flex-1.p-4.overflow-y-auto');
                    if (messagesContainer) {
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                }
            }" x-init="scrollMessages()" x-on:livewire:updated="scrollMessages()">
                @forelse ($messages as $message )
                <div class="flex {{  $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs px-4 py-2 rounded-2xl shadow
                    {{  $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 dark:bg-zinc-700 dark:text-white' }}">
                        @if ($selectedChat instanceof \App\Models\Group && $message->sender_id !== auth()->id())
                            <small class="font-semibold text-gray-600 dark:text-zinc-300">{{ $message->sender->name }}:</small><br>
                        @endif
                        {{ $message->message}}
                    </div>
                </div>
                @empty
                    <div class="text-center text-gray-500 dark:text-zinc-400">
                        @if ($selectedChat)
                            Start a conversation!
                        @else
                            Select a chat to view messages.
                        @endif
                    </div>
                @endforelse
            </div>

            <div id="Typing-indicator" class="px-4 pb-1 text-xs text-gray-400 italic dark:text-zinc-500"></div>

            @if ($selectedChat)
                <form wire:submit.prevent="submit" class="p-4 border-t bg-white flex items-center gap-2 dark:bg-zinc-900 dark:border-zinc-700">
                    <input
                        wire:model="newMessage"
                        type="text"
                        class="flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300 dark:bg-zinc-800 dark:border-zinc-600 dark:text-white"
                        placeholder="Type your message..." />
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">
                        Send
                    </button>
                </form>
            @else
                <div class="p-4 border-t bg-white flex items-center justify-center text-gray-500 dark:bg-zinc-900 dark:border-zinc-700 dark:text-zinc-400">
                    Please select a chat to start messaging.
                </div>
            @endif
        </div>
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
