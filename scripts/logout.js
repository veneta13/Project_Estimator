const onLoad = () => {
    let logout = window.confirm('Желаете ли да се отпишете?');

    if (logout) {
        fetch('../php/login.php', {
            method: 'DELETE'
        })
            .then(r => {
                location.replace("../index.html");
            })
    } else {
        location.replace("./profile.html");
    }
}

document.addEventListener('DOMContentLoaded', onLoad, false);
