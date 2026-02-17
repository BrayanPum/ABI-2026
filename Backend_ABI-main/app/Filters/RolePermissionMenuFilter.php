<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use TakiElias\Tablar\Menu\Filters\FilterInterface;

class RolePermissionMenuFilter implements FilterInterface
{
    public function transform($item)
    {
        return $this->isVisible($item) ? ($item['header'] ?? $item) : false;
    }

    protected function isVisible($item)
    {
        $user = Auth::user();

        if (isset($item['hasAnyRole']) && !$user->hasAnyRole($item['hasAnyRole'])) {
            return false;
        }

        if (isset($item['hasRole']) && !$user->hasRole($item['hasRole'])) {
            return false;
        }

        return true;
    }
}
