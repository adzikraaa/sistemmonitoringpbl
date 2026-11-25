@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const message = @json(session('success'));
        const candidates = Array.from(document.querySelectorAll(
            '.figma-alert, .sim-alert, [role="alert"], #milestoneSuccessAlert'
        ));
        const successAlert = candidates.find(element => element.textContent.trim().includes(message));

        if (!successAlert) return;

        const dismiss = () => {
            if (successAlert.isConnected) successAlert.remove();
        };

        window.setTimeout(dismiss, 5000);

        document.addEventListener('click', function (event) {
            const editTrigger = event.target.closest(
                '.sim-edit-btn, [title^="Edit"], [data-action="edit"], [data-edit-kelompok], button[onclick*="pilih"], button[onclick*="Edit"]'
            );
            const actionElement = event.target.closest('button, a');
            const hasEditLabel = actionElement?.textContent.trim().toLowerCase().startsWith('edit');

            if (editTrigger || hasEditLabel) dismiss();
        });
    });
</script>
@endif
