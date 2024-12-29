document.querySelector('form').addEventListener('submit', function(event) {
    let isValid = true;
    const fields = ['last_name', 'first_name', 'id_card', 'password', 'phone', 'gender', 'birthday'];
    let firstInvalidField = null;

    fields.forEach(id => {
        const input = document.getElementById(id);
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            if (!firstInvalidField) {
                firstInvalidField = input;
            }
        } else {
            input.classList.remove('error');
        }
    });

    if (!isValid) {
        event.preventDefault();
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        alert('請填寫所有必填欄位！');
    }
});