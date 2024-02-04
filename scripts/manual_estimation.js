const loadData = () => {
    fetch('../php/manual_estimation.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            console.log("Fetch response:", r.result);

            if (r.result.length !== 0) {
                const project_name = document.getElementById("project_name");
                project_name.value = r.result['name'];

                const preset = document.getElementById("preset");
                preset.value = r.result['type'];
            }
        });
}

const saveProject = () => {
    event.preventDefault();

    const formData = new FormData(document.getElementById('project_form'));

    fetch('../php/manual_estimation.php', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                window.alert("Промените са запазени успешно.")
            } else {
                window.alert("Възникна грешка. Моля, опитайте отново.")
            }
        });
};

const saveTask = () => {
    event.preventDefault();

    const formData = new FormData(document.getElementById('task_form'));

    fetch('../php/manual_estimation.php', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                window.alert("Промените са запазени успешно.")
            } else {
                window.alert("Възникна грешка. Моля, опитайте отново.")
            }
        });
};

document.addEventListener('DOMContentLoaded', loadData, false);
