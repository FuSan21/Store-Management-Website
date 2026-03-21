<?php

/*
 * The JS bundle imports Livewire from public/vendor/livewire because Vercel
 * builds the assets before Composer runs. The copy must match the installed
 * package, or the browser and server speak different Livewire versions.
 */
test('the published livewire script matches the installed package', function () {
    $published = base_path('public/vendor/livewire/livewire.esm.js');
    $installed = base_path('vendor/livewire/livewire/dist/livewire.esm.js');

    expect(file_exists($published))->toBeTrue('Run: php artisan vendor:publish --tag=livewire:assets --force')
        ->and(md5_file($published))->toBe(md5_file($installed), 'Run: php artisan vendor:publish --tag=livewire:assets --force');
});
