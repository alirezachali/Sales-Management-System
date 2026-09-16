<?php

namespace Tests\Feature;

use App\Livewire\Messages\MessageInbox;
use App\Livewire\Messages\MessageManager;
use App\Livewire\Messages\MessagesBell;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * در AppServiceProvider برای هر مجوز دیتابیس یک Gate همنام ساخته می‌شود؛
     * چون در تست‌ها جدول مجوزها بعد از بوت برنامه پر می‌شود، همان کار
     * اینجا تکرار می‌شود تا میدل‌ور can: روی روت‌ها قابل آزمودن باشد.
     */
    private function registerPermissionGates(): void
    {
        foreach (Permission::pluck('name') as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    private function makeRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'display_name' => $name]);
        $group = PermissionGroup::create(['name' => 'پیام‌ها '.$name]);

        foreach ($permissions as $permission) {
            $role->permissions()->attach(
                Permission::create([
                    'permission_group_id' => $group->id,
                    'name' => $permission,
                    'display_name' => $permission,
                ])->id
            );
        }

        $this->registerPermissionGates();

        return $role;
    }

    private function makeUser(string $username, Role $role, bool $isActive = true): User
    {
        return User::create([
            'name' => 'کاربر '.$username,
            'username' => $username,
            'email' => $username.'@example.com',
            'phone' => '09120000000',
            'password' => 'secret-password',
            'role_id' => $role->id,
            'is_active' => $isActive,
        ]);
    }

    private function admin(): User
    {
        return $this->makeUser('admin1', $this->makeRole('admin', [
            'messages.view',
            'messages.create',
            'messages.delete',
        ]));
    }

    public function test_manager_can_send_message_to_a_specific_user(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver1', $this->makeRole('cashier'));

        Livewire::actingAs($admin)
            ->test(MessageManager::class)
            ->call('openCreateModal')
            ->set('audience', Message::AUDIENCE_SINGLE)
            ->set('recipient_id', (string) $receiver->id)
            ->set('subject', 'اطلاعیه مهم')
            ->set('body', 'متن پیام آزمایشی')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $message = Message::firstOrFail();

        $this->assertSame($admin->id, $message->sender_id);
        $this->assertSame(Message::AUDIENCE_SINGLE, $message->audience);
        $this->assertSame('اطلاعیه مهم', $message->subject);

        $this->assertDatabaseHas('message_recipients', [
            'message_id' => $message->id,
            'user_id' => $receiver->id,
            'read_at' => null,
        ]);
    }

    public function test_broadcast_message_reaches_all_active_users_except_sender(): void
    {
        $admin = $this->admin();
        $active1 = $this->makeUser('active1', $this->makeRole('r1'));
        $active2 = $this->makeUser('active2', $this->makeRole('r2'));
        $inactive = $this->makeUser('inactive1', $this->makeRole('r3'), isActive: false);

        Livewire::actingAs($admin)
            ->test(MessageManager::class)
            ->call('openCreateModal')
            ->set('audience', Message::AUDIENCE_ALL)
            ->set('body', 'پیام گروهی')
            ->call('save')
            ->assertHasNoErrors();

        $message = Message::firstOrFail();

        $recipientIds = $message->recipients()->pluck('user_id')->sort()->values()->all();

        $this->assertSame(
            collect([$active1->id, $active2->id])->sort()->values()->all(),
            $recipientIds
        );
        $this->assertNotContains($admin->id, $recipientIds);
        $this->assertNotContains($inactive->id, $recipientIds);
    }

    public function test_sending_to_a_specific_user_requires_selecting_a_user(): void
    {
        $admin = $this->admin();

        Livewire::actingAs($admin)
            ->test(MessageManager::class)
            ->call('openCreateModal')
            ->set('audience', Message::AUDIENCE_SINGLE)
            ->set('recipient_id', null)
            ->set('body', 'متن پیام')
            ->call('save')
            ->assertHasErrors(['recipient_id' => 'required']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_opening_a_message_in_inbox_records_read_time(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver2', $this->makeRole('cashier2'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'subject' => 'سلام',
            'body' => 'متن',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        $recipient = MessageRecipient::create([
            'message_id' => $message->id,
            'user_id' => $receiver->id,
        ]);

        $this->assertNull($recipient->read_at);

        Livewire::actingAs($receiver)
            ->test(MessageInbox::class)
            ->call('open', $recipient->id)
            ->assertSet('showDetailsModal', true);

        $this->assertNotNull($recipient->fresh()->read_at);
    }

    public function test_user_cannot_open_a_message_addressed_to_someone_else(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver3', $this->makeRole('cashier3'));
        $stranger = $this->makeUser('stranger1', $this->makeRole('cashier4'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'body' => 'متن خصوصی',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        $recipient = MessageRecipient::create([
            'message_id' => $message->id,
            'user_id' => $receiver->id,
        ]);

        Livewire::actingAs($stranger)
            ->test(MessageInbox::class)
            ->call('open', $recipient->id)
            ->assertSet('showDetailsModal', false);

        $this->assertNull($recipient->fresh()->read_at);
    }

    public function test_inbox_mark_all_as_read_updates_unread_messages(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver4', $this->makeRole('cashier5'));

        foreach (range(1, 3) as $i) {
            $message = Message::create([
                'sender_id' => $admin->id,
                'body' => 'پیام '.$i,
                'audience' => Message::AUDIENCE_SINGLE,
            ]);

            MessageRecipient::create(['message_id' => $message->id, 'user_id' => $receiver->id]);
        }

        $this->assertSame(3, $receiver->unreadMessagesCount());

        Livewire::actingAs($receiver)
            ->test(MessageInbox::class)
            ->call('markAllAsRead');

        $this->assertSame(0, $receiver->unreadMessagesCount());
    }

    public function test_manager_sees_who_read_the_message(): void
    {
        $admin = $this->admin();
        $reader = $this->makeUser('reader1', $this->makeRole('cashier6'));
        $lazy = $this->makeUser('lazy1', $this->makeRole('cashier7'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'subject' => 'گزارش خوانده‌شدن',
            'body' => 'متن',
            'audience' => Message::AUDIENCE_ALL,
        ]);

        $readRecipient = MessageRecipient::create(['message_id' => $message->id, 'user_id' => $reader->id]);
        MessageRecipient::create(['message_id' => $message->id, 'user_id' => $lazy->id]);

        $readRecipient->markAsRead();

        Livewire::actingAs($admin)
            ->test(MessageManager::class)
            ->call('openDetails', $message->id)
            ->assertSet('showDetailsModal', true)
            ->assertSee($reader->name)
            ->assertSee($lazy->name)
            ->assertSee('خوانده‌شده')
            ->assertSee('خوانده‌نشده');

        $message->refresh()->load('recipients');
        $this->assertSame(1, $message->read_count);
        $this->assertSame(1, $message->unread_count);
        $this->assertFalse($message->is_fully_read);
    }

    public function test_user_without_permission_cannot_open_message_management_page(): void
    {
        $user = $this->makeUser('plain1', $this->makeRole('cashier8'));

        $this->actingAs($user)
            ->get(route('messages.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_for_inbox(): void
    {
        $this->get(route('messages.inbox'))->assertRedirect(route('login'));
    }

    public function test_inbox_page_renders_with_received_messages(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver6', $this->makeRole('cashier10'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'subject' => 'عنوان پیام من',
            'body' => 'متن پیام من',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        MessageRecipient::create(['message_id' => $message->id, 'user_id' => $receiver->id]);

        $this->actingAs($receiver)
            ->get(route('messages.inbox'))
            ->assertOk()
            ->assertSee('پیام‌های من')
            ->assertSee('عنوان پیام من');
    }

    public function test_messages_bell_shows_unread_messages(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver7', $this->makeRole('cashier11'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'subject' => 'پیام زنگ',
            'body' => 'متن زنگ',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        MessageRecipient::create(['message_id' => $message->id, 'user_id' => $receiver->id]);

        Livewire::actingAs($receiver)
            ->test(MessagesBell::class)
            ->assertSet('count', 1)
            ->call('toggle')
            ->assertSet('open', true)
            ->assertSee('پیام زنگ');
    }

    public function test_manager_page_renders_for_authorized_user(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver8', $this->makeRole('cashier12'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'subject' => 'پیام مدیریت',
            'body' => 'متن مدیریت',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        MessageRecipient::create(['message_id' => $message->id, 'user_id' => $receiver->id]);

        $this->actingAs($admin)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('پیام‌های ارسال‌شده')
            ->assertSee('پیام مدیریت')
            ->assertSee($receiver->name);
    }

    public function test_manager_can_delete_a_sent_message_and_its_recipients(): void
    {
        $admin = $this->admin();
        $receiver = $this->makeUser('receiver5', $this->makeRole('cashier9'));

        $message = Message::create([
            'sender_id' => $admin->id,
            'body' => 'برای حذف',
            'audience' => Message::AUDIENCE_SINGLE,
        ]);

        MessageRecipient::create(['message_id' => $message->id, 'user_id' => $receiver->id]);

        Livewire::actingAs($admin)
            ->test(MessageManager::class)
            ->call('confirmDelete', $message->id)
            ->call('delete');

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('message_recipients', 0);
    }
}
