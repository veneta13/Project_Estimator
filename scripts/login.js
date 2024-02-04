const login = () => {
    event.preventDefault();

    const formData = new FormData(document.getElementById('login_form'));
    fetch('../php/login.php', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(r => {
            console.log("Fetch response:", r.result);
            if (r.result) {
                location.replace("./profile.html");
            } else {
                window.alert('Грешка при вход, моля, проверете данните и опитайте отново!');
            }
        });
};
