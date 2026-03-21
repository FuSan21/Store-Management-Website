<x-layouts.admin title="Coupons">
    <x-slot:actions>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><x-icon name="plus" :size="16"/> New coupon</a>
    </x-slot:actions>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Code</th><th>Discount</th><th>Minimum</th><th>Used</th><th>Valid</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        @php($reason = $coupon->rejectionReason(PHP_FLOAT_MAX))
                        <tr>
                            <td>
                                <span class="rounded bg-surface px-2 py-1 font-mono text-sm font-semibold">{{ $coupon->code }}</span>
                                @if ($coupon->description)<span class="mt-1 block text-xs text-muted">{{ $coupon->description }}</span>@endif
                            </td>
                            <td class="font-medium">{{ $coupon->summary() }}</td>
                            <td class="text-muted">{{ $coupon->min_subtotal ? money($coupon->min_subtotal) : '—' }}</td>
                            <td class="tabular-nums">{{ $coupon->used_count }}{{ $coupon->max_uses ? ' / '.$coupon->max_uses : '' }}</td>
                            <td class="text-sm whitespace-nowrap text-muted">
                                {{ $coupon->starts_at?->format('j M Y') ?? 'Now' }} – {{ $coupon->expires_at?->format('j M Y') ?? 'no end' }}
                            </td>
                            <td>
                                @if ($reason === null)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-ok-soft px-2.5 py-1 text-xs font-medium text-ok"><span class="size-1.5 rounded-full bg-current"></span> Live</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface px-2.5 py-1 text-xs font-medium text-muted" title="{{ $reason }}"><span class="size-1.5 rounded-full bg-current"></span> {{ Str::of($reason)->after('This code ')->before('.')->ucfirst() }}</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-icon size-8" aria-label="Edit {{ $coupon->code }}"><x-icon name="pencil" :size="16"/></a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline" x-data x-confirm="Delete coupon {{ $coupon->code }}?">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon size-8 text-muted hover:text-accent" aria-label="Delete {{ $coupon->code }}"><x-icon name="trash" :size="16"/></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-12 text-center text-muted">No coupons yet. Create one for a launch, a festival or a returning customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line p-4">{{ $coupons->links() }}</div>
    </div>
</x-layouts.admin>
