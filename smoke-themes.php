<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "ThemeService class: " . get_class(app(App\Services\ThemeService::class)) . PHP_EOL;
echo "slider default: " . config('themes.slider.default') . PHP_EOL;
echo "feature default: " . config('themes.feature.default') . PHP_EOL;
echo "testimonial default: " . config('themes.testimonial.default') . PHP_EOL;

$t = app(App\Services\ThemeService::class);
echo "component(slider) -> " . $t->component('slider') . PHP_EOL;
echo "component(feature) -> " . $t->component('feature') . PHP_EOL;
echo "component(testimonial) -> " . $t->component('testimonial') . PHP_EOL;
echo "sections(): " . implode(',', array_keys($t->sections())) . PHP_EOL;
echo "slider designs: " . json_encode($t->available('slider')) . PHP_EOL;
echo "setting('missing', 'default') -> " . var_export(setting('missing', 'default'), true) . PHP_EOL;
echo "formatDateBN('2026-03-19') -> " . formatDateBN('2026-03-19') . PHP_EOL;
