const loadData = () => {
    fetch('../php/profile.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            console.log("Fetch response:", r.result);
            if (r.result) {
                document.getElementById("username").innerHTML = r.result['name'];
                document.getElementById("tasks").innerHTML = r.result['task_count'];
                document.getElementById("time").innerHTML = r.result['task_time'];
                document.getElementById("invitations").innerHTML = r.result['invitations'];
            } else {
                window.alert('Грешка при вход, моля, проверете данните и опитайте отново!');
            }
        });
}

document.addEventListener('DOMContentLoaded', loadData, false);
