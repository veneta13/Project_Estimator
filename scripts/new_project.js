const rerouteManual = () => {
    fetch('../php/new_project.php', {
        method: 'POST'
    })
        .then(r => {
            location.replace("./manual_estimation.html");
        });
}

const rerouteAutomatic = () => {
    fetch('../php/new_project.php', {
        method: 'POST'
    })
        .then(r => {
            location.replace("./automatic_estimation.html");
        });
}
