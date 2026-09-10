<div
    x-data="subscriptionModal({
        workspaceId: @json($workspaceId ?? null),
        workspaceName: @json($workspaceName ?? null),
    })"
    x-on:open-subscription-modal.window="openModal($event.detail.workspaceId, $event.detail.workspaceName)"
    x-on:close-subscription-modal.window="close()"
    x-cloak
>
    <!-- Modal backdrop -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm"
        @click="close()"
        aria-hidden="true"
    ></div>

    <!-- Modal panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
    >
        <div class="relative w-full max-w-lg bg-[#111214] border border-white/[0.08] rounded-2xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-white/[0.08] px-6 py-4">
                <div>
                    <h2 id="modal-title" class="text-lg font-bold tracking-tight text-white">
                        Add subscription<span x-show="workspaceName"> to <span x-text="workspaceName"></span></span>
                    </h2>
                    <p class="mt-0.5 text-xs text-[#68685f]" x-show="workspaceId">
                        This subscription will be linked to your workspace.
                    </p>
                </div>
                <button @click="close()" class="rounded-lg p-1.5 text-[#68685f] hover:text-white hover:bg-white/10 transition" aria-label="Close modal">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-5">
                @include('subscriptions._form')
            </div>
        </div>
    </div>
</div>