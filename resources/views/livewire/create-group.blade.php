<div class="flex flex-col items-start w-full">
    <div class="text-center mb-6 w-full shadow-2xl rounded-2xl">
        <flux:heading size="xl" level="1">{{ __('Create New Academic Group') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Create a group for your course discussions.') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="w-full">
        <form wire:submit="createGroup" class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow-sm space-y-6 dark:bg-zinc-800 dark:border-zinc-700">
            <flux:input
                wire:model="name"
                :label="__('Group Name')"
                type="text"
                required
                autofocus
                placeholder="e.g., Dasar Pemrograman A"
            />

            <flux:textarea
                wire:model="description"
                :label="__('Description (Optional)')"
                placeholder="A brief description of the group"
                style="resize: none;"
            ></flux:textarea>
            
            <div class="flux-field">
    <label class="flux-label">{{ __('Select Members') }}</label>
                
        <div class="space-y-2 max-h-60 overflow-y-auto border rounded-md p-4 dark:bg-zinc-700 dark:border-zinc-600">
            @foreach ($users as $user)
                <label class="flex items-center space-x-2">
                    <input
                        type="checkbox"
                        wire:model="selectedMembers"
                        value="{{ $user->id }}"
                        class="flux-control"
                    >
                    <span>{{ $user->name }} ({{ $user->email }})</span>
                </label>
            @endforeach
        </div>

        @error('selectedMembers') 
            <span class="text-red-500 text-xs">{{ $message }}</span> 
        @enderror
    </div>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Create Group') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>