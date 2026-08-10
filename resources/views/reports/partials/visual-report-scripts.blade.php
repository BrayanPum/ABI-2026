<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = Array.from(document.querySelectorAll('[data-chart-target][data-chart-group]'));

        if (buttons.length === 0) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                const group = button.getAttribute('data-chart-group');
                const target = button.getAttribute('data-chart-target');
                const groupButtons = Array.from(document.querySelectorAll('[data-chart-target][data-chart-group="' + group + '"]'));
                const groupPanels = Array.from(document.querySelectorAll('[data-chart-panel][data-chart-group="' + group + '"]'));

                groupButtons.forEach(function (item) {
                    item.classList.toggle('is-active', item === button);
                });

                groupPanels.forEach(function (panel) {
                    panel.classList.toggle('is-active', panel.getAttribute('data-chart-panel') === target);
                });
            });
        });
    });
</script>
