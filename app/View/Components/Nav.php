<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Nav extends Component
{
    public array $items;
    public ?string $active;

    public function __construct()
    {
        $this->items  = $this->prepareItems(config('nav'));
        $this->active = Route::currentRouteName();
    }

    public function render()
    {
        return view('components.nav');
    }

    protected function prepareItems(array $items): array
    {
        foreach ($items as $key => $item) {
            if (
                isset($item['ability']) &&
                Gate::denies($item['ability'])
            ) {
                unset($items[$key]);
            }
        }

        return $items;
    }
}
