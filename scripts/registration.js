const register = () => {
    event.preventDefault();

    const formData = new FormData(document.getElementById('registration_form'));

    console.log(formData);

    fetch('../php/register.php',  {method:'POST', body:formData})
        .then(r=>r.json())
        .then(r=> {
            console.log("Fetch response:", r.result);
            if (r.result) {
                location.replace("./login.html");
            } else {
                window.alert('Грешка при регистрацията!');
            }
        });
};