const loadData = () => {
    fetch('../php/profile.php',  {method:'GET'})
        .then(r=>r.json())
        .then(r=> {
            console.log("Fetch response:", r.result);
            if (r.result) {
                document.getElementById("username").innerHTML = r.result;
            } else {
                window.alert('Грешка при вход, моля, проверете данните и опитайте отново!');
            }
        });
}

document.addEventListener('DOMContentLoaded', loadData, false);
