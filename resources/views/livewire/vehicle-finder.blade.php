<form wire:submit="find" class="card p-5 shadow-xl shadow-black/5 sm:p-6">
    <div class="flex items-center gap-2.5">
        <span class="grid size-9 place-items-center rounded-lg bg-accent text-accent-ink"><x-icon name="car" :size="18"/></span>
        <h2 class="text-lg font-semibold">Find filters for your car</h2>
    </div>

    <div class="mt-5 grid gap-3">
        <div>
            <label for="vf-brand" class="label">Make</label>
            <select id="vf-brand" wire:model.live="brand" class="input">
                <option value="">Choose a make</option>
                @foreach ($this->brands as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="vf-model" class="label">Model</label>
            <select id="vf-model" wire:model.live="model" class="input" @disabled($brand === '')>
                <option value="">{{ $brand === '' ? 'Choose a make first' : 'Choose a model' }}</option>
                @foreach ($this->models as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="vf-fit" class="label">Engine and years</label>
            <select id="vf-fit" wire:model.live="fit" class="input" @disabled($model === '')>
                <option value="">{{ $model === '' ? 'Choose a model first' : 'Any engine' }}</option>
                @foreach ($this->fitments as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="vf-collection" class="label">Part</label>
            <select id="vf-collection" wire:model.live="collection" class="input">
                <option value="">Every filter</option>
                @foreach ($this->collections as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg mt-5 w-full">
        <span wire:loading.remove wire:target="brand,model,fit,collection">Show {{ $this->matches }} {{ Str::plural('filter', $this->matches) }}</span>
        <span wire:loading wire:target="brand,model,fit,collection">Checking fit…</span>
        <x-icon name="arrow-right" :size="18"/>
    </button>
</form>
