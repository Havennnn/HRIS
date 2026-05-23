<?php

declare(strict_types=1);

namespace App\Services\Admin;

use Illuminate\Http\Request;

class DropdownService
{
    /**
     * Get options from a given class.
     *
     * @param  class-string  $class  FQCN with static options() method
     * @param  Request|null  $request  Optional, for ?search= filtering
     * @return array<int, array{value: int|string, label: string}>
     */
    public function get(string $class, ?Request $request = null): array
    {
        if (! class_exists($class) || ! method_exists($class, 'options')) {
            return [];
        }

        $options = $class::options();

        if ($request && $search = $request->input('search')) {
            $search = strtolower($search);
            $options = array_filter($options, fn ($opt) => str_contains(strtolower($opt['label']), $search));
            $options = array_values($options);
        }

        return $options;
    }
}
