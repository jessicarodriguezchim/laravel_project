<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;

/**
 * Fixes Rappasoft Livewire Tables toolbar actions:
 * - clearSearch: also resets pagination (the package only cleared the string).
 * - clearSorts: restores the default sort; an empty sorts array removed all ORDER BY.
 */
abstract class BaseDataTable extends DataTableComponent
{
    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetComputedPage();
    }

    public function clearSorts(): void
    {
        $this->sorts = [];
        $defaultColumn = $this->getDefaultSortColumn();
        if ($this->sortingIsEnabled() && $this->hasDefaultSort() && $defaultColumn !== null) {
            $this->setSort($defaultColumn, $this->getDefaultSortDirection());
        }
        $this->resetComputedPage();
    }
}
