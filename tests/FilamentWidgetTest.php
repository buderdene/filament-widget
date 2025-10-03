<?php

it('can install widget migrations and configurations', function () {
    $this->artisan('filament-widget:install')
        ->expectsOutput('Publishing widget Configuration...')
        ->expectsOutput('Publishing Filament widget Migrations...')
        ->expectsOutput('Filament widget was installed successfully.')
        ->assertExitCode(0);
});
