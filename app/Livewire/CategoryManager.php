<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all'; // 'all', 'active', 'archived'

    // Create / Edit modal state
    public bool $showModal = false;
    public ?int $editingCategoryId = null;
    public string $name = '';
    public string $description = '';
    public bool $is_active = true;

    public string $successMessage = '';
    public string $errorMessage = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name,' . $this->editingCategoryId,
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->editingCategoryId = null;
        $this->name = '';
        $this->description = '';
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $category = Category::withTrashed()->findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->is_active = (bool)$category->is_active;
        $this->showModal = true;
    }

    public function saveCategory(): void
    {
        $this->validate();

        $data = [
            'name' => trim($this->name),
            'slug' => Str::slug($this->name),
            'description' => trim($this->description) ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingCategoryId) {
            $category = Category::withTrashed()->findOrFail($this->editingCategoryId);
            $oldName = $category->name;
            $category->update($data);

            // Synchronize product category string for backward compatibility
            if ($oldName !== $category->name) {
                Product::where('category_id', $category->id)
                    ->orWhere('category', $oldName)
                    ->update([
                        'category' => $category->name,
                        'category_id' => $category->id,
                    ]);
            }

            $this->successMessage = "Category '{$category->name}' successfully updated.";
        } else {
            $data['created_by'] = Auth::id();
            $category = Category::create($data);
            $this->successMessage = "New category '{$category->name}' successfully created.";
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'editingCategoryId', 'is_active']);
    }

    public function archiveCategory(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => false]);
        $category->delete(); // Soft delete

        $this->successMessage = "Category '{$category->name}' has been archived.";
    }

    public function restoreCategory(int $id): void
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        $category->update(['is_active' => true]);

        $this->successMessage = "Category '{$category->name}' has been restored.";
    }

    public function toggleActiveStatus(int $id): void
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $statusText = $category->is_active ? 'activated' : 'deactivated';
        $this->successMessage = "Category '{$category->name}' has been {$statusText}.";
    }

    public function render()
    {
        $query = Category::withTrashed()
            ->withCount(['products as active_products_count' => function ($q) {
                $q->where('status', 'active');
            }]);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'active') {
            $query->whereNull('deleted_at')->where('is_active', true);
        } elseif ($this->statusFilter === 'archived') {
            $query->where(function ($q) {
                $q->whereNotNull('deleted_at')->orWhere('is_active', false);
            });
        }

        $categories = $query->orderBy('name', 'asc')->paginate(12);

        $totalCount = Category::withTrashed()->count();
        $activeCount = Category::whereNull('deleted_at')->where('is_active', true)->count();
        $archivedCount = Category::onlyTrashed()->count();

        return view('livewire.category-manager', [
            'categories' => $categories,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'archivedCount' => $archivedCount,
        ]);
    }
}
