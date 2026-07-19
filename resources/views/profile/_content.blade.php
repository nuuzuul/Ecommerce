<div class="space-y-6">
    <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
        @include('profile.partials.update-password-form')
    </div>
    <div class="rounded-3xl border border-red-100 bg-white p-6 shadow-sm">
        @include('profile.partials.delete-user-form')
    </div>
</div>
