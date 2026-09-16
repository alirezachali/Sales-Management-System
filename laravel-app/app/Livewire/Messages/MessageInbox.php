<?php

namespace App\Livewire\Messages;

use App\Models\MessageRecipient;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * صندوق پیام‌های کاربر: پیام‌هایی که مدیر برای او فرستاده است.
 * با باز کردن هر پیام، تاریخ و ساعت خوانده‌شدن آن ثبت می‌شود.
 */
class MessageInbox extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $filterRead = '';
    public string $search = '';

    public bool $showDetailsModal = false;
    public ?int $detailsId = null;

    public function updatingFilterRead(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['filterRead', 'search']);
        $this->resetPage();
    }

    /** باز کردن پیام و ثبت خوانده‌شدن آن */
    public function open(int $id): void
    {
        $recipient = $this->findRecipient($id);

        if (! $recipient) {
            return;
        }

        $recipient->markAsRead();

        $this->detailsId = $recipient->id;
        $this->showDetailsModal = true;
    }

    public function closeModal(): void
    {
        $this->showDetailsModal = false;
        $this->detailsId = null;
    }

    public function markAllAsRead(): void
    {
        $updated = MessageRecipient::where('user_id', auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        session()->flash(
            'success',
            $updated > 0 ? $updated.' پیام خوانده‌شده علامت خورد.' : 'پیام خوانده‌نشده‌ای وجود ندارد.'
        );
    }

    /**
     * رکورد گیرنده‌ی متعلق به کاربر جاری؛ تا کاربر نتواند پیام دیگران را باز کند.
     */
    private function findRecipient(int $id): ?MessageRecipient
    {
        return MessageRecipient::with('message.sender')
            ->where('user_id', auth()->id())
            ->find($id);
    }

    public function render()
    {
        $query = MessageRecipient::with('message.sender:id,name')
            ->where('user_id', auth()->id());

        if ($this->filterRead === 'unread') {
            $query->unread();
        } elseif ($this->filterRead === 'read') {
            $query->whereNotNull('read_at');
        }

        $term = trim($this->search);

        if ($term !== '') {
            $query->whereHas('message', function ($q) use ($term) {
                $q->search($term);
            });
        }

        $detailsRecipient = $this->detailsId ? $this->findRecipient($this->detailsId) : null;

        return view('livewire.messages.message-inbox', [
            'inbox' => $query->latest()->paginate(10),
            'detailsRecipient' => $detailsRecipient,
            'unreadCount' => MessageRecipient::where('user_id', auth()->id())->unread()->count(),
            'totalCount' => MessageRecipient::where('user_id', auth()->id())->count(),
        ]);
    }
}
