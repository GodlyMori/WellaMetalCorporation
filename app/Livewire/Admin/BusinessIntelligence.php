<?php

namespace App\Livewire\Admin;

use App\Services\BusinessIntelligenceService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Executive Business Intelligence')]
class BusinessIntelligence extends Component
{
    public string $datePreset = 'this_year';

    public string $activeTab = 'overview';

    public function mount(): void
    {
        if (! auth()->user()?->hasRole('admin')) {
            abort(403, 'Unauthorized. Business Intelligence is strictly restricted to System Administrators.');
        }
    }

    public function setDatePreset(string $preset): void
    {
        if (in_array($preset, ['this_year', 'last_30_days', 'this_quarter', 'last_year', 'all_time'])) {
            $this->datePreset = $preset;
        }
    }

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'negotiations', 'inventory_abc', 'customers'])) {
            $this->activeTab = $tab;
        }
    }

    public function render(BusinessIntelligenceService $biService)
    {
        $metrics = $biService->getExecutiveMetrics($this->datePreset);

        return view('livewire.admin.business-intelligence', [
            'metrics' => $metrics,
        ]);
    }
}
