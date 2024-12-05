<div>
    <div class="py-2">
        <x-button label="Back" icon="arrow-left" emerald sm wire:click="returnToMembers"/>
    </div>

      {{-- <x-button label="Reset" icon="upload" dark sm wire:click="resetArea"/>  --}}
     <div class="border p-4">
        <h1>Upload Members</h1>
        <input type="file" wire:model="masterlist" />
        <x-button label="Upload" icon="upload" dark sm wire:click="uploadMembers"/>
    </div>
    <div class="border p-4">
        <h1>Upload Areas</h1>
        <input type="file" wire:model="area" />
        <x-button label="Upload" icon="upload" dark sm wire:click="uploadArea"/>

    </div> 
    {{-- <div class="border p-4">
        <h1>Update Members</h1>
        <input type="file" wire:model="member_update" />
        <x-button label="Import" icon="download" dark sm wire:click="updateMember"/>
    </div>
    <div class="border p-4">
        <h1>Update Area</h1>
        <input type="file" wire:model="area_update" />
        <x-button label="Import" icon="download" dark sm wire:click="updateArea"/>
    </div> --}}
</div>
