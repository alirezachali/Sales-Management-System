<?php

namespace App\Livewire\Cashboxes;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Services\CashboxService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CashboxManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public ?int $selectedId = null;

    public string $filterType = 'all';

    public string $dateFromJalali = '';
    public string $dateToJalali = '';

    // فرم صندوق
    public bool $showFormModal = false;
    public ?int $cashboxId = null;
    public string $name = '';
    public string $type = 'cash';
    public ?string $account_number = null;
    public ?string $iban = null;
    public ?string $bank_name = null;
    public $opening_balance = 0;
    public bool $is_default = false;
    public bool $is_active = true;
    public ?string $notes = null;

    // فرم تراکنش
    public bool $showTxModal = false;
    public string $txType = 'deposit';
    public $txAmount = 0;
    public ?string $txDescription = null;
    public string $txToCashbox = '';

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['cash', 'bank', 'wallet', 'other'])],
            'account_number' => ['nullable', 'string', 'max:50'],
            'iban' => ['nullable', 'string', 'max:40'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'is_default' => ['boolean'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function txRules(): array
    {
        return [
            'txType' => ['required', Rule::in(['deposit', 'withdraw', 'transfer', 'adjustment'])],
            'txAmount' => ['required', 'numeric'],
            'txDescription' => ['nullable', 'string', 'max:255'],
            'txToCashbox' => ['nullable', 'different:selectedId', 'exists:cashboxes,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'نام صندوق الزامی است.',
            'txAmount.required' => 'مبلغ را وارد کنید.',
            'txAmount.min' => 'مبلغ باید بزرگ‌تر از صفر باشد.',
            'txToCashbox.different' => 'صندوق مقصد نباید همان صندوق فعلی باشد.',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $cashbox = Cashbox::findOrFail($id);

        $this->cashboxId = $cashbox->id;
        $this->name = $cashbox->name;
        $this->type = $cashbox->type;
        $this->account_number = $cashbox->account_number;
        $this->iban = $cashbox->iban;
        $this->bank_name = $cashbox->bank_name;
        $this->opening_balance = $cashbox->opening_balance;
        $this->is_default = (bool) $cashbox->is_default;
        $this->is_active = (bool) $cashbox->is_active;
        $this->notes = $cashbox->notes;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeAction($this->cashboxId ? 'cashboxes.edit' : 'cashboxes.create');

        $validated = $this->validate();

        if ($this->cashboxId) {
            $cashbox = Cashbox::findOrFail($this->cashboxId);
            $cashbox->update($validated);
        } else {
            $cashbox = Cashbox::create($validated);
        }

        if ($validated['is_default']) {
            Cashbox::where('id', '!=', $cashbox->id)->update(['is_default' => false]);
        }

        session()->flash('success', $this->cashboxId ? 'صندوق به‌روزرسانی شد.' : 'صندوق جدید ثبت شد.');

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function viewCashbox(int $id): void
    {
        $this->selectedId = $id;
        $this->resetPage();
    }

    public function openTxModal(string $type): void
    {
        $this->authorizeAction(match ($type) {
            'deposit' => 'cashboxes.deposit',
            'withdraw' => 'cashboxes.withdraw',
            'transfer' => 'cashboxes.transfer',
            'adjustment' => 'cashboxes.adjust',
        });

        $this->reset(['txAmount', 'txDescription', 'txToCashbox']);
        $this->txType = $type;
        $this->showTxModal = true;
    }

    public function saveTx(CashboxService $service): void
    {
        $cashbox = Cashbox::findOrFail($this->selectedId);

        $data = $this->validate($this->txRules());

        try {
            if ($data['txType'] === 'transfer') {
                $to = Cashbox::findOrFail($data['txToCashbox']);

                $service->transfer($cashbox, $to, (float) $data['txAmount'], $data['txDescription']);
            } else {
                $service->transaction(
                    $cashbox,
                    $data['txType'],
                    (float) $data['txAmount'],
                    $data['txDescription'],
                );
            }

            session()->flash('success', 'تراکنش صندوق ثبت شد.');
        } catch (\InvalidArgumentException $e) {
            $this->addError('txAmount', $e->getMessage());

            return;
        }

        $this->showTxModal = false;
        $this->reset(['txAmount', 'txDescription', 'txToCashbox']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('cashboxes.delete');

        $cashbox = Cashbox::findOrFail($this->deletingId);

        if ($cashbox->is_default) {
            session()->flash('error', 'حذف صندوق پیش‌فرض مجاز نیست.');
            $this->showDeleteModal = false;

            return;
        }

        if (abs((float) $cashbox->balance) > 0.001) {
            session()->flash('error', 'صندوق دارای موجودی است؛ ابتدا آن را تسویه کنید.');
            $this->showDeleteModal = false;

            return;
        }

        $cashbox->delete();
        session()->flash('success', 'صندوق حذف شد.');

        if ($this->selectedId === $this->deletingId) {
            $this->selectedId = null;
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showTxModal = false;
        $this->showDeleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'cashboxId', 'name', 'account_number', 'iban', 'bank_name', 'opening_balance', 'notes',
        ]);
        $this->type = 'cash';
        $this->is_default = false;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $cashboxes = Cashbox::withCount('transactions')->orderBy('is_default', 'desc')->orderBy('name')->get();

        $selected = $this->selectedId ? Cashbox::find($this->selectedId) : null;

        $transactions = collect();

        if ($selected) {
            $transactions = CashboxTransaction::with(['cashbox:id,name', 'user:id,name'])
                ->where('cashbox_id', $selected->id)
                ->when($this->filterType !== 'all', fn ($q) => $q->where('type', $this->filterType))
                ->when(jalaliToGregorian($this->dateFromJalali), fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
                ->when(jalaliToGregorian($this->dateToJalali), fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
                ->latest()
                ->paginate(20);
        }

        return view('livewire.cashboxes.cashbox-manager', [
            'cashboxes' => $cashboxes,
            'selected' => $selected,
            'transactions' => $transactions,
            'totalBalance' => (float) Cashbox::where('is_active', true)->sum('balance'),
            'typeLabels' => CashboxTransaction::typesWithLabels(),
        ]);
    }
}
