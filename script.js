document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('search');
    const noteItems = document.querySelectorAll('.note-item');
    const noResults = document.getElementById('no-results');

    if (search) {
        search.addEventListener('input', () => {
            const query = search.value.trim().toLocaleLowerCase('ro');
            let visibleCount = 0;

            noteItems.forEach((item) => {
                const title = item.dataset.title || '';
                const matches = title.includes(query);

                item.classList.toggle('hidden', !matches);

                if (matches) {
                    visibleCount++;
                }
            });

            if (noResults) {
                noResults.classList.toggle(
                    'hidden',
                    visibleCount !== 0 || noteItems.length === 0
                );
            }
        });
    }

    const content = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    const limitWarning = document.getElementById('limit-warning');

    function updateCounter() {
        if (!content || !charCount) return;

        charCount.textContent = content.value.length;

        if (limitWarning) {
            limitWarning.classList.toggle(
                'hidden',
                content.value.length < 10000
            );
        }
    }

    if (content) {
        content.addEventListener('input', updateCounter);
        updateCounter();
    }

    const deleteButton = document.getElementById('delete-button');
    const deleteForm = document.getElementById('delete-form');

    if (deleteButton && deleteForm) {
        deleteButton.addEventListener('click', () => {
            const confirmed = window.confirm(
                'Ești sigur că dorești să ștergi această notă? Acțiunea este ireversibilă.'
            );

            if (confirmed) {
                deleteForm.requestSubmit();
            }
        });
    }

    const noteForm = document.getElementById('note-form');

    if (noteForm && content) {
        noteForm.addEventListener('submit', (event) => {
            if (content.value.length > 10000) {
                event.preventDefault();
                alert('Nota nu poate depăși 10.000 de caractere.');
            }
        });
    }
});