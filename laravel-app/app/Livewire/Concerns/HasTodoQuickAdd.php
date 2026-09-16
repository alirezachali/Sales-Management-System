<?php

namespace App\Livewire\Concerns;

use App\Models\Todo;
use Illuminate\Validation\Rule;

/**
 * منطق مشترک کارت «لیست کارهای من» برای داشبوردهای نقش‌ها
 * (افزودن کار جدید از مودال + تکمیل/لغو کارهای خود کاربر).
 */
trait HasTodoQuickAdd
{
    /*
    |--------------------------------------------------------------------|
    |              فیلدهای فرم افزودن کار جدید (مودال کارت کارها)          |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public string $title = '';
    public ?string $description = null;
    public string $priority = Todo::PRIORITY_MEDIUM;
    /** تاریخ سررسید شمسی برای نمایش و انتخاب توسط کاربر (مثل 1405/06/11) */
    public ?string $due_date_jalali = null;
    public ?string $due_date = null;

    public array $priorityLabels = [
        'low' => 'کم',
        'medium' => 'متوسط',
        'high' => 'زیاد',
    ];

    /** کارهای انجام‌نشده‌ی کاربر جاری برای کارت لیست کارها */
    protected function currentUserTodos()
    {
        return Todo::with('assignee')
            ->where('assigned_to', auth()->id())
            ->where('status', '!=', Todo::STATUS_COMPLETED)
            ->latest()
            ->take(10)
            ->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->title = '';
        $this->description = null;
        $this->priority = Todo::PRIORITY_MEDIUM;
        $this->due_date = null;
        $this->due_date_jalali = null;
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    | همگام‌سازی تاریخ شمسی ورودی کاربر با تاریخ میلادی سمت سرور          |
    |--------------------------------------------------------------------|
    */
    public function updatedDueDateJalali(): void
    {
        $val = trim((string) $this->due_date_jalali);

        if ($val === '') {
            $this->due_date = null;
            $this->resetErrorBag('due_date_jalali');
            return;
        }

        $gregorian = jalaliToGregorian($val);

        if ($gregorian !== null) {
            $this->due_date = $gregorian;
            $this->resetErrorBag('due_date_jalali');
        }
    }

    /** ثبت کار جدید؛ انجام‌دهنده‌ی کار خودِ همین کاربر است. */
    public function save(): void
    {
        $this->authorizeAction('todos.create');

        $val = trim((string) $this->due_date_jalali);

        if ($val !== '') {
            $gregorian = jalaliToGregorian($val);

            if ($gregorian === null) {
                $this->addError('due_date_jalali', 'تاریخ سررسید معتبر نیست. مثال درست: 1405/06/11');
                return;
            }

            $this->due_date = $gregorian;
        } else {
            $this->due_date = null;
        }

        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(array_keys($this->priorityLabels))],
        ]);

        Todo::create([
            'user_id' => auth()->id(),
            'assigned_to' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => Todo::STATUS_PENDING,
            'priority' => $data['priority'],
            'due_date' => $this->due_date,
        ]);

        session()->flash('success', 'کار جدید با موفقیت ثبت شد');

        $this->showFormModal = false;
        $this->resetForm();
    }

    /** تکمیل/لغو تکمیل یک کار */
    public function toggleComplete(int $id): void
    {
        $this->authorizeAction('todos.edit');

        $todo = Todo::where('assigned_to', auth()->id())->findOrFail($id);
        $todo->toggleComplete();
    }
}
