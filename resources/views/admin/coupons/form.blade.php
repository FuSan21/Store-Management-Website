<x-layouts.admin :title="$coupon->exists ? 'Edit coupon '.$coupon->code : 'New coupon'">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost"><x-icon name="chevron-left" :size="16"/> Coupons</a>
    </x-slot:actions>

    <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" class="card max-w-2xl space-y-5 p-6"
        x-data="{ type: @js(old('type', $coupon->type?->value ?? 'percent')) }">
        @csrf
        @if ($coupon->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Code" name="code" :value="$coupon->code" class="font-mono" placeholder="EID2026" required hint="Customers type this at checkout. Letters, numbers, dashes."/>
            <x-field label="Description (internal)" name="description" :value="$coupon->description" placeholder="Eid sale"/>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Type" name="type" type="select" x-model="type">
                @foreach (\App\Enums\CouponType::cases() as $case)
                    <option value="{{ $case->value }}" @selected(old('type', $coupon->type?->value) === $case->value)>{{ $case->label() }}</option>
                @endforeach
            </x-field>
            <div>
                <x-field name="value" type="number" step="0.01" min="0.01" :value="$coupon->value" required x-bind:max="type === 'percent' ? 100 : null"
                    label="Value"/>
                <p class="mt-1.5 text-xs text-muted" x-text="type === 'percent' ? 'Percent off the subtotal' : 'Taka off the subtotal'"></p>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Minimum subtotal (optional)" name="min_subtotal" type="number" step="0.01" min="0" :value="$coupon->min_subtotal"/>
            <x-field label="Total uses allowed (optional)" name="max_uses" type="number" min="1" :value="$coupon->max_uses" :hint="$coupon->exists ? 'Used '.$coupon->used_count.' times so far.' : null"/>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Starts (optional)" name="starts_at" type="datetime-local" :value="$coupon->starts_at?->format('Y-m-d\TH:i')"/>
            <x-field label="Ends (optional)" name="expires_at" type="datetime-local" :value="$coupon->expires_at?->format('Y-m-d\TH:i')"/>
        </div>
        <label class="flex items-center gap-2.5 text-sm">
            <input type="checkbox" name="is_active" value="1" class="checkbox" @checked(old('is_active', $coupon->is_active))> Active
        </label>
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary">{{ $coupon->exists ? 'Save coupon' : 'Create coupon' }}</button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.admin>
