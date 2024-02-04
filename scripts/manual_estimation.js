const loadData = () => {
    fetch('../php/manual_estimation.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            console.log("Fetch response:", r.result);

            if (r.result.length !== 0) {
                const project_name = document.getElementById("project_name");
                project_name.value = r.result[0]['name'];

                const preset = document.getElementById("preset");
                preset.value = r.result[0]['type'];

                if (r.result[1].length !== 0) {
                    let item;

                    const parent = document.getElementById('task_table').getElementsByTagName('tbody')[0];

                    for (let index in r.result[1]) {
                        item = r.result[1][index];

                        let new_row = parent.insertRow();

                        let name_cell = new_row.insertCell();
                        name_cell.innerText = item['name'];
                        name_cell.classList.add('table-text');

                        let time_cell = new_row.insertCell();
                        time_cell.innerText = item['time'];
                        time_cell.classList.add('table-text');

                        let user_cell = new_row.insertCell();
                        user_cell.innerText = item['user'];
                        user_cell.classList.add('table-text');

                        let edit_cell = new_row.insertCell();
                        edit_cell.classList.add('table-text');

                        let edit_button = document.createElement('button');
                        edit_button.innerText = 'Промени';
                        edit_button.classList.add('table-button');
                        edit_button.classList.add('edit-button');
                        edit_button.value = item['task_id'];

                        edit_cell.appendChild(edit_button);

                        let delete_cell = new_row.insertCell();
                        delete_cell.classList.add('table-text');

                        let delete_button = document.createElement('button');
                        delete_button.innerText = 'Изтрий';
                        delete_button.classList.add('table-button');
                        delete_button.classList.add('delete-button');
                        delete_button.value = item['task_id'];

                        delete_cell.appendChild(delete_button);
                    }
                }
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
