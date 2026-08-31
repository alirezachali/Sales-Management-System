<?php

namespace App\Livewire\Todos;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterStatus = '';
    public string $filterPriority = '';
    public string $filterAssignee = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $title = '';
    public ?string $description = null;
    public string $status = 'pending';
    public string $priority = 'medium';
    public ?string $due_date = null;
    public ?string $assigned_to = null;

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public bool $showDetailsModal = false;
    public ?int $detailsId = null;

    public array $statusLabels = [
        'pending' => 'در انتظار',
        'in_progress' => 'در حال انجام',
        'completed' => 'تکمیل شده',
    ];

    public array $priorityLabels = [
        'low' => 'کم',
        'medium' => 'متوسط',
        'high' => 'زیاد',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPriority(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAssignee(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterPriority', 'filterAssignee']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys($this->statusLabels))],
            'priority' => ['required', Rule::in(array_keys($this->priorityLabels))],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'وارد کردن عنوان کار الزامی است.',
            'status.required' => 'انتخاب وضعیت الزامی است.',
            'priority.required' => 'انتخاب اولویت الزامی است.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                            مودال کارها                             |
    |--------------------------------------------------------------------|
    */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $todo = Todo::findOrFail($id);

        $this->editingId = $todo->id;
        $this->title = $todo->title;
        $this->description = $todo->description;
        $this->status = $todo->status;
        $this->priority = $todo->priority;
        $this->due_date = $todo->due_date?->toDateString();
        $this->assigned_to = $todo->assigned_to ? (string) $todo->assigned_to : null;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->detailsId = $id;
        $this->showDetailsModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showDetailsModal = false;
        $this->deletingId = null;
        $this->detailsId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->description = null;
        $this->status = Todo::STATUS_PENDING;
        $this->priority = Todo::PRIORITY_MEDIUM;
        $this->due_date = null;
        // به‌صورت پیش‌فرض، کار برای خودِ کاربرِ ثبت‌کننده ساخته می‌شود؛
        // کاربر می‌تواند آن را به هر کاربر دیگری (مثلاً یک مدیر برای کارمندش) واگذار کند.
        $this->assigned_to = (string) auth()->id();
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                          ذخیره (افزودن/ویرایش)                      |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        $data = $this->validate();

        $data['assigned_to'] = $data['assigned_to'] ?: null;

        if ($this->editingId) {
            Todo::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'کار با موفقیت ویرایش شد');
        } else {
            Todo::create(array_merge($data, ['user_id' => auth()->id()]));
            session()->flash('success', 'کار جدید با موفقیت ثبت شد');
        }

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Todo::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'کار حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                           تکمیل/لغو                                 |
    |--------------------------------------------------------------------|
    */
    public function toggleComplete(int $id): void
    {
        $todo = Todo::findOrFail($id);
        $todo->toggleComplete();
    }

    /*
    |--------------------------------------------------------------------|
    |                                رندر                                 |
    |--------------------------------------------------------------------|
    */
    public function render()
    {
        $query = Todo::with(['user', 'assignee']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority !== '') {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterAssignee !== '') {
            $query->where('assigned_to', $this->filterAssignee);
        }

        $todos = $query->latest()->paginate(15);

        $detailsTodo = $this->detailsId
            ? Todo::with(['user', 'assignee'])->find($this->detailsId)
            : null;

        return view('livewire.todos.todo-manager', [
            'todos' => $todos,
            'allUsers' => User::orderBy('name')->get(['id', 'name']),
            'detailsTodo' => $detailsTodo,
            'counts' => [
                'total' => Todo::count(),
                'pending' => Todo::pending()->count(),
                'in_progress' => Todo::inProgress()->count(),
                'completed' => Todo::completed()->count(),
                'high_priority_pending' => Todo::pending()->highPriority()->count(),
                'due_soon' => Todo::dueSoon()->count(),
            ],
        ]);
    }
}