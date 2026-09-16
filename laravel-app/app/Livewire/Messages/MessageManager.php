<?php

namespace App\Livewire\Messages;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * صفحه‌ی مدیریت پیام‌ها برای مدیر:
 * ارسال پیام به یک کاربر خاص یا به همه کاربران، و مشاهده‌ی فهرست پیام‌های
 * ارسال‌شده همراه با وضعیت خوانده‌شدن هر گیرنده.
 */
class MessageManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterAudience = '';
    /** '' همه | read همه خوانده‌اند | unread حداقل یک گیرنده نخوانده است */
    public string $filterRead = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم ارسال پیام                       |
    |--------------------------------------------------------------------|
    */
    /** single: یک کاربر خاص | all: همه کاربران فعال */
    public string $audience = Message::AUDIENCE_SINGLE;
    public ?string $recipient_id = null;
    public ?string $subject = null;
    public string $body = '';

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDetailsModal = false;
    public bool $showDeleteModal = false;
    public ?int $detailsId = null;
    public ?int $deletingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAudience(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRead(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterAudience', 'filterRead']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'audience' => ['required', Rule::in([Message::AUDIENCE_SINGLE, Message::AUDIENCE_ALL])],
            'recipient_id' => [
                Rule::requiredIf($this->audience === Message::AUDIENCE_SINGLE),
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'audience.required' => 'انتخاب نوع گیرندگان الزامی است.',
            'recipient_id.required' => 'انتخاب کاربر گیرنده الزامی است.',
            'recipient_id.exists' => 'کاربر انتخاب‌شده معتبر نیست.',
            'body.required' => 'متن پیام را وارد کنید.',
            'body.max' => 'متن پیام نباید بیشتر از ۵۰۰۰ کاراکتر باشد.',
            'subject.max' => 'موضوع نباید بیشتر از ۲۵۵ کاراکتر باشد.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                                |
    |--------------------------------------------------------------------|
    */
    public function openCreateModal(): void
    {
        $this->authorizeAction('messages.create');
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->authorizeAction('messages.view');
        $this->detailsId = $id;
        $this->showDetailsModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizeAction('messages.delete');
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDetailsModal = false;
        $this->showDeleteModal = false;
        $this->detailsId = null;
        $this->deletingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->audience = Message::AUDIENCE_SINGLE;
        $this->recipient_id = null;
        $this->subject = null;
        $this->body = '';
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                            ارسال پیام                               |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        $this->authorizeAction('messages.create');

        $data = $this->validate();

        $recipientIds = $this->resolveRecipientIds();

        if ($recipientIds === []) {
            $this->addError('recipient_id', 'کاربری برای ارسال پیام یافت نشد.');
            return;
        }

        DB::transaction(function () use ($data, $recipientIds) {
            $message = Message::create([
                'sender_id' => auth()->id(),
                'subject' => $data['subject'] ?: null,
                'body' => $data['body'],
                'audience' => $data['audience'],
            ]);

            $now = now();

            MessageRecipient::insert(
                collect($recipientIds)->map(fn (int $userId) => [
                    'message_id' => $message->id,
                    'user_id' => $userId,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );
        });

        session()->flash('success', 'پیام برای '.count($recipientIds).' کاربر ارسال شد.');

        $this->closeModals();
        $this->resetPage();
    }

    /**
     * شناسه‌ی گیرندگان پیام بر اساس نوع ارسال:
     * همه کاربران فعال (به‌جز خودِ فرستنده) یا تنها کاربر انتخاب‌شده.
     *
     * @return array<int, int>
     */
    private function resolveRecipientIds(): array
    {
        if ($this->audience === Message::AUDIENCE_ALL) {
            return User::where('is_active', true)
                ->whereKeyNot(auth()->id())
                ->pluck('id')
                ->all();
        }

        // کاربر باید فعال باشد تا پیام واقعاً به دستش برسد
        return User::where('is_active', true)
            ->whereKey($this->recipient_id)
            ->pluck('id')
            ->all();
    }

    public function delete(): void
    {
        $this->authorizeAction('messages.delete');

        if ($this->deletingId) {
            // گیرندگان با cascade در دیتابیس حذف می‌شوند
            Message::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'پیام حذف شد.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                                رندر                                 |
    |--------------------------------------------------------------------|
    */
    public function render()
    {
        $query = Message::with(['sender:id,name', 'recipients.user:id,name'])
            ->search($this->search);

        if ($this->filterAudience !== '') {
            $query->where('audience', $this->filterAudience);
        }

        if ($this->filterRead === 'read') {
            $query->whereDoesntHave('recipients', fn ($q) => $q->whereNull('read_at'));
        } elseif ($this->filterRead === 'unread') {
            $query->whereHas('recipients', fn ($q) => $q->whereNull('read_at'));
        }

        $messages = $query->latest()->paginate(15);

        $detailsMessage = $this->detailsId
            ? Message::with(['sender:id,name', 'recipients.user:id,name'])->find($this->detailsId)
            : null;

        return view('livewire.messages.message-manager', [
            'messages' => $messages,
            'detailsMessage' => $detailsMessage,
            'recipients' => $this->recipientsForSelect(),
            'counts' => $this->counts(),
        ]);
    }

    /**
     * کاربران فعالی که می‌توان پیام به آن‌ها فرستاد
     * (خودِ فرستنده در فهرست نمی‌آید چون پیام گروهی هم او را در بر نمی‌گیرد).
     */
    private function recipientsForSelect()
    {
        return User::where('is_active', true)
            ->whereKeyNot(auth()->id())
            ->orderBy('name')
            ->get(['id', 'name', 'username']);
    }

    private function counts(): array
    {
        return [
            'messages' => Message::count(),
            'delivered' => MessageRecipient::count(),
            'read' => MessageRecipient::whereNotNull('read_at')->count(),
            'unread' => MessageRecipient::whereNull('read_at')->count(),
        ];
    }
}
