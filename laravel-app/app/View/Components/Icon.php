<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct(
        public string $name,
        public int|string|null $size = null,
        public string|int|null $stroke = null,
        public string $variant = 'outline',
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.icon');
    }
}