const addTasks = (result) => {
    const parent = document.getElementById('task_table').getElementsByTagName('tbody')[0];

    for (let index in result) {
        let item = result[index];

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

        let type_cell = new_row.insertCell();
        type_cell.innerText = item['type'];
        type_cell.classList.add('table-text');
    }
}

const loadData = () => {
    fetch('../php/manual_estimation.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            const project_name = document.getElementById('project_name');
            project_name.innerText = r.result[0]['name'];

            if (r.result.length !== 0) {
                if (r.result[1].length !== 0) {
                    addTasks(r.result[1]);
                }
            }
        });
}

document.addEventListener('DOMContentLoaded', loadData, false);
