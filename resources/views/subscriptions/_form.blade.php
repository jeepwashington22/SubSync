<form
    x-data="subscriptionForm({
        name: @json($name ?? old('name')),
        billingCycle: @json($billingCycle ?? old('billing_cycle', 'monthly')),
        billingDate: @json($billingDate ?? old('billing_date')),
        category: @json($category ?? old('category')),
        workspaceId: @json($workspaceId ?? old('workspace_id')),
    })"
    x-on:subscription-workspace.window="workspaceId = $event.detail.workspaceId"
    @submit.prevent="submit()"
    action="{{ route('subscriptions.store') }}"
    class="space-y-5"
    novalidate
>
    @csrf

    {{-- Name --}}
    <div>
        <label for="name" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Service Name</label>
        <input id="name" type="text" name="name" x-model="name" required autofocus
               class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
               placeholder="e.g., Netflix, Spotify, AWS">
        <template x-if="errors.name">
            <p class="text-xs text-red-400 mt-1" x-text="errors.name"></p>
        </template>
    </div>

    {{-- Price & Billing Cycle --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="price" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Price (₱)</label>
            <input id="price" type="number" step="0.01" name="price" x-model="price" required
                   class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
                   placeholder="249.00">
            <template x-if="errors.price">
                <p class="text-xs text-red-400 mt-1" x-text="errors.price"></p>
            </template>
        </div>

        <div>
            <label for="billing_cycle" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Billing Cycle</label>
            <select id="billing_cycle" name="billing_cycle" x-model="billingCycle" required
                    class="w-full bg-[#1a2625] border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition">
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
                <option value="weekly">Weekly</option>
            </select>
            <template x-if="errors.billing_cycle">
                <p class="text-xs text-red-400 mt-1" x-text="errors.billing_cycle"></p>
            </template>
        </div>
    </div>

    {{-- Billing Date & Category --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="billing_date" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Next Billing Date</label>
            <input id="billing_date" type="date" name="billing_date" x-model="billingDate" required
                   class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition">
            <template x-if="errors.billing_date">
                <p class="text-xs text-red-400 mt-1" x-text="errors.billing_date"></p>
            </template>
        </div>

        <div>
            <label for="category" class="block text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-1">Category (Optional)</label>
            <input id="category" type="text" name="category" x-model="category"
                   class="w-full bg-black/30 border border-gray-600/50 rounded-lg text-white focus:border-teal-400 focus:ring-1 focus:ring-teal-400 px-4 py-2.5 transition placeholder-gray-500"
                   placeholder="Entertainment, Utility">
            <template x-if="errors.category">
                <p class="text-xs text-red-400 mt-1" x-text="errors.category"></p>
            </template>
        </div>
    </div>

    {{-- Hidden workspace_id (only in modal context) --}}
    <template x-if="workspaceId">
        <input type="hidden" name="workspace_id" :value="workspaceId">
    </template>

    {{-- Actions --}}
    <div class="pt-4 flex items-center justify-end gap-3">
        <button type="button" @click="close()" class="px-5 py-2.5 rounded-lg text-sm text-gray-400 hover:text-white transition" x-show="canClose">Cancel</button>
        <button type="submit" :disabled="submitting" class="bg-teal-600 hover:bg-teal-500 disabled:bg-teal-900 disabled:opacity-50 text-white font-medium text-sm px-6 py-2.5 rounded-lg shadow-lg shadow-teal-900/20 transition disabled:cursor-not-allowed flex items-center gap-2">
            <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="submitting ? 'Saving…' : 'Save Subscription'"></span>
        </button>
    </div>
</form>