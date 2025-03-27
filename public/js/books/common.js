document.addEventListener('DOMContentLoaded', function() {
    const isbnInput = document.querySelector('input[name="ISBN"]');
    if (isbnInput) {
        isbnInput.focus();
    }
}); 