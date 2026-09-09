<?php

namespace App\Livewire\Debts;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Debt;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DebtManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    // Filters
    public string $search = '';
    public string $filterStatus = 'all';

    // Modal state
    public bool $showModal = false;
    public bool $showPayModal = false;
    public bool $showDeleteModal = false;

    // Form fields
    public ?int $editingId = null;
    public string $title = '';
    public string $creditor_name = '';
    public string $creditor_type = 'other';
    public ?int $supplier_id = null;
    public string $amount = '';
    public string $due_date = '';
    public string $notes = '';

    // Payment
    public ?int $payingDebtId = null;
    public string $pay_amount = '';

    // Delete
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'creditor_name'  => 'required|string|max:255',
            'creditor_type'  => 'required|in:supplier,other',
            'supplier_id'    => 'nullable|exists:suppliers,id',
            'amount'         => 'required|integer|min:1',
            'due_date'       => 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // Open add modal
    public function openAddModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    // Open edit modal
    public function openEditModal(int $id): void
    {
        $debt = Debt::findOrFail($id);
        $this->editingId    = $debt->id;
        $this->title        = $debt->title;
        $this->creditor_name = $debt->creditor_name;
        $this->creditor_type = $debt->creditor_type;
        $this->supplier_id  = $debt->supplier_id;
        $this->amount       = (string) $debt->amount;
        $this->due_date     = $debt->due_date ? $debt->due_date->format('Y-m-d') : '';
        $this->notes        = $debt->notes ?? '';
        $this->showModal    = true;
    }

    // Save (add or edit)
    public function save(): void
    {
        $this->authorizeAction($this->editingId ? 'debts.edit' : 'debts.create');

        $this->validate();

        $data = [
            'title'         => $this->title,
            'creditor_name' => $this->creditor_name,
            'creditor_type' => $this->creditor_type,
            'supplier_id'   => $this->creditor_type === 'supplier' ? $this->supplier_id : null,
            'amount'        => (int) $this->amount,
            'due_date'      => $this->due_date ?: null,
            'notes'         => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Debt::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'بدهی با موفقیت ویرایش شد.');
        } else {
            $data['paid_amount'] = 0;
            Debt::create($data);
            session()->flash('success', 'بدهی با موفقیت ثبت شد.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    // Open pay modal
    public function openPayModal(int $id): void
    {
        $this->payingDebtId = $id;
        $this->pay_amount   = '';
        $this->showPayModal = true;
    }

    // Record payment
    public function recordPayment(): void
    {
        $this->authorizeAction('debts.settle');

        $this->validate([
            'pay_amount' => 'required|integer|min:1',
        ]);

        $debt = Debt::findOrFail($this->payingDebtId);
        $newPaid = $debt->paid_amount + (int) $this->pay_amount;

        if ($newPaid > $debt->amount) {
            $this->addError('pay_amount', 'مبلغ پرداختی از بدهی بیشتر است.');
            return;
        }

        $debt->update(['paid_amount' => $newPaid]);

        session()->flash('success', 'پرداخت با موفقیت ثبت شد.');
        $this->showPayModal = false;
        $this->payingDebtId = null;
        $this->pay_amount   = '';
    }

    // Confirm delete
    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    // Delete
    public function delete(): void
    {
        $this->authorizeAction('debts.delete');

        Debt::findOrFail($this->deletingId)->delete();
        session()->flash('success', 'بدهی با موفقیت حذف شد.');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function closeModal(): void
    {
        $this->showModal       = false;
        $this->showPayModal    = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId     = null;
        $this->title         = '';
        $this->creditor_name = '';
        $this->creditor_type = 'other';
        $this->supplier_id   = null;
        $this->amount        = '';
        $this->due_date      = '';
        $this->notes         = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $debts = Debt::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('creditor_name', 'like', "%{$this->search}%");
            }))
            ->byStatus($this->filterStatus)
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->paginate(15);

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'   => Debt::whereIn('status', ['unpaid', 'partial'])->sum('amount'),
            'paid'    => Debt::whereIn('status', ['unpaid', 'partial'])->sum('paid_amount'),
            'overdue' => Debt::whereIn('status', ['unpaid', 'partial'])
                             ->whereNotNull('due_date')
                             ->whereDate('due_date', '<', now())
                             ->count(),
        ];
        $stats['remaining'] = $stats['total'] - $stats['paid'];

        return view('livewire.debts.debt-manager', compact('debts', 'suppliers', 'stats'));
    }
}