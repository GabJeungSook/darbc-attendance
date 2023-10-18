<div>
    <div class="border p-4">
        <h1>Upload Members</h1>
        <input type="file" wire:model="masterlist" />
        <x-button label="Upload" icon="upload" dark sm wire:click="uploadMembers"/>
    </div>
    <div class="border p-4">
        <h1>Upload Areas</h1>
        <input type="file" wire:model="area" />
        <x-button label="Upload" icon="upload" dark sm wire:click="uploadArea"/>
        {{-- <x-button label="Reset" icon="upload" dark sm wire:click="resetArea"/> --}}
    </div>
</div>
