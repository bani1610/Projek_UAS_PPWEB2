<div class="flex flex-col items-start w-full">
        <div class="relative mb-6 w-full">
            <flux:heading size="xl" level="1">{{ __('Create New Academic Group') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('Create a group for your course discussions.') }}</flux:subheading>
            <flux:separator variant="subtle" />
        </div>
        <div class="w-full"">
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
                    <label for="members" class="flux-label">{{ __('Select Members') }}</label>
                    <select multiple wire:model="selectedMembers" id="members"
                        class="flux-control block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600 dark:text-white"
                        size="{{ $users->count() > 0 ? $users->count() : 1 }}"
                    >
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('selectedMembers') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>


                <div class="flex items-center justify-end">
                    <flux:button type="submit" variant="primary" class="w-full">
                        {{ __('Create Group') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
