document.addEventListener('alpine:init', () => {
    Alpine.data('subscriptionModal', (initialValues = {}) => ({
        open: false,
        workspaceId: initialValues.workspaceId ?? null,
        workspaceName: initialValues.workspaceName ?? null,
        openModal(workspaceId = null, workspaceName = null) {
            this.workspaceId = workspaceId;
            this.workspaceName = workspaceName;
            this.open = true;
            this.$dispatch('subscription-workspace', { workspaceId });
        },
        close() {
            this.open = false;
        },
    }));

    Alpine.data('subscriptionForm', (initialValues = {}) => ({
        name: initialValues.name ?? '',
        price: initialValues.price ?? '',
        billingCycle: initialValues.billingCycle ?? 'monthly',
        billingDate: initialValues.billingDate ?? '',
        category: initialValues.category ?? '',
        workspaceId: initialValues.workspaceId ?? null,
        errors: {},
        submitting: false,
        canClose: true,
        async submit() {
            this.submitting = true;
            this.errors = {};

            const response = await fetch(this.$el.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    name: this.name,
                    price: this.price,
                    billing_cycle: this.billingCycle,
                    billing_date: this.billingDate,
                    category: this.category,
                    workspace_id: this.workspaceId,
                }),
            });

            if (response.ok) {
                window.location.href = (await response.json()).redirect;
                return;
            }

            if (response.status === 422) {
                this.errors = Object.fromEntries(
                    Object.entries((await response.json()).errors).map(([field, messages]) => [field, messages[0]]),
                );
            }

            this.submitting = false;
        },
        close() {
            this.$dispatch('close-subscription-modal');
        },
    }));
});