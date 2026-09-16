<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfilePage extends Component
{
    use WithFileUploads;

    // شناسه کاربری که پروفایل او نمایش داده می‌شود (از مسیر URL استخراج می‌شود)
    public int $userId = 0;

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم ویرایش                           |
    |--------------------------------------------------------------------|
    */
    public bool $editing = false;

    public string $name = '';
    public string $username = '';
    public ?string $email = null;
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // تصویر پروفایل (آپلود موقت) و مسیر تصویر فعلی
    public $avatar = null;
    public ?string $currentAvatar = null;

    public function mount(string $username): void
    {
        // نام کاربری در URL ممکن است با حروف بزرگ/کوچک متفاوت وارد شود
        $user = User::whereRaw('LOWER(username) = ?', [mb_strtolower($username)])->firstOrFail();

        // فقط خود کاربر یا کسی که مجوز مشاهده کاربران را دارد
        if (auth()->id() !== $user->id && ! auth()->user()->hasPermission('users.view')) {
            abort(403);
        }

        $this->userId = $user->id;
        $this->fillForm();
    }

    private function user(): User
    {
        return User::findOrFail($this->userId);
    }

    private function fillForm(): void
    {
        $user           = $this->user();
        $this->name     = $user->name;
        $this->username = $user->username;
        $this->email    = $user->email;
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->avatar   = null;
        $this->currentAvatar = $user->avatar;
    }

    public function startEditing(): void
    {
        $this->ensureOwnProfile();
        $this->fillForm();
        $this->resetErrorBag();
        $this->editing = true;
    }

    public function cancelEditing(): void
    {
        $this->editing = false;
        $this->fillForm();
    }

    protected function rules(): array
    {
        // تغییر رمز عبور اختیاری است؛ اگر رمز جدیدی وارد نشود رمز فعلی دست‌نخورده می‌ماند
        $passwordRules = ['nullable', 'confirmed', Password::min(6)];
        $currentPasswordRules = ['nullable'];

        if ($this->password !== '') {
            $passwordRules[0] = 'required';
            $currentPasswordRules = ['required', 'current_password'];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($this->userId),
            ],
            'email' => [
                'nullable', 'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'current_password' => $currentPasswordRules,
            'password' => $passwordRules,
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'                => 'وارد کردن نام نمایشی الزامی است.',
            'username.required'            => 'وارد کردن نام کاربری الزامی است.',
            'username.unique'              => 'این نام کاربری قبلاً ثبت شده است.',
            'username.regex'               => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، عدد، نقطه، خط تیره و زیرخط باشد.',
            'email.email'                  => 'ایمیل وارد شده معتبر نیست.',
            'email.unique'                 => 'این ایمیل قبلاً ثبت شده است.',
            'avatar.image'                 => 'فایل انتخابی باید یک تصویر باشد.',
            'avatar.mimes'                 => 'فرمت مجاز تصویر: jpg, jpeg, png, webp',
            'avatar.max'                   => 'حجم تصویر نباید بیشتر از ۲ مگابایت باشد.',
            'current_password.required'    => 'برای تغییر رمز عبور، وارد کردن رمز فعلی الزامی است.',
            'current_password.current_password' => 'رمز عبور فعلی اشتباه است.',
            'password.confirmed'           => 'رمز عبور جدید و تکرار آن یکسان نیستند.',
            'password.min'                 => 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد.',
        ];
    }

    public function save()
    {
        $this->ensureOwnProfile();

        $this->validate();

        $user = $this->user();

        $user->name     = $this->name;
        $user->username = $this->username;
        $user->email    = $this->email ?: null;

        // آپلود/جایگزینی تصویر پروفایل
        if ($this->avatar) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $this->avatar->store('avatars', 'public');
        }

        // تغییر رمز عبور فقط در صورتی که رمز جدید وارد شده باشد
        if ($this->password !== '') {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        // ریدایرکت کامل تا ناوبار (تصویر/نام) هم بلافاصله با مقادیر جدید رندر شود
        return redirect()
            ->route('profile.show', $user->username)
            ->with('success', 'پروفایل با موفقیت به‌روزرسانی شد.');
    }

    private function ensureOwnProfile(): void
    {
        if (auth()->id() !== $this->userId) {
            abort(403, 'امکان ویرایش پروفایل سایر کاربران وجود ندارد.');
        }
    }

    public function render()
    {
        return view('livewire.profile.profile-page', [
            'user' => $this->user()->load('employee', 'role'),
        ]);
    }
}
