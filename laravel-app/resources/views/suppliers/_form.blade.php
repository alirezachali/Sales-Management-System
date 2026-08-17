@php
    $s = $supplier ?? null;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">نام <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $s->name ?? '') }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">نام شرکت</label>
        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $s->company_name ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">نام شخص رابط</label>
        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $s->contact_person ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">نوع <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            <option value="individual" @selected(old('type', $s->type ?? 'individual') === 'individual')>حقیقی</option>
            <option value="company" @selected(old('type', $s->type ?? '') === 'company')>حقوقی</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">موبایل <span class="text-danger">*</span></label>
        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $s->mobile ?? '') }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label">تلفن ثابت</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $s->phone ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">ایمیل</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $s->email ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">استان</label>
        <input type="text" name="province" class="form-control" value="{{ old('province', $s->province ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">شهر</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $s->city ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">آدرس</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $s->address ?? '') }}</textarea>
    </div>

    <div class="col-md-4">
        <label class="form-label">کد پستی</label>
        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $s->postal_code ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">کد ملی</label>
        <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $s->national_id ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">کد اقتصادی</label>
        <input type="text" name="economic_code" class="form-control" value="{{ old('economic_code', $s->economic_code ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">سقف اعتباری (ریال)</label>
        <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $s->credit_limit ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">مونده اولیه حساب (ریال)</label>
        <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $s->opening_balance ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">شماره شبا</label>
        <input type="text" name="iban" class="form-control" value="{{ old('iban', $s->iban ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">شرایط پرداخت</label>
        <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $s->payment_terms ?? '') }}" placeholder="مثلاً: اعتباری ۳۰ روزه">
    </div>

    <div class="col-md-6">
        <label class="form-label">امتیاز (۱ تا ۵)</label>
        <input type="number" min="1" max="5" name="rating" class="form-control" value="{{ old('rating', $s->rating ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">یادداشت</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $s->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" class="form-check-input" id="is_active_{{ $s->id ?? 'new' }}"
            name="is_active" value="1" @checked(old('is_active', $s->is_active ?? true))>
        <label class="form-check-label" for="is_active_{{ $s->id ?? 'new' }}">فعال</label>
    </div>
</div>
