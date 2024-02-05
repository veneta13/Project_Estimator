const loadData = () => {
    fetch('../php/my_projects.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                let item;
                const parent = document.getElementById("my_projects");

                for (let index in r.result) {
                    item = r.result[index];
                    const child_section = document.createElement("section");
                    parent.appendChild(child_section);
                    child_section.classList.add('project-section');

                    const project_name = document.createElement("h1");
                    child_section.appendChild(project_name);
                    project_name.classList.add('project-title');
                    project_name.innerText = item['name'];

                    const project_owner = document.createElement("p");
                    child_section.appendChild(project_owner);
                    project_owner.classList.add('project-owner');
                    project_owner.innerText = item['owner'];

                    const button_section = document.createElement("section");
                    button_section.classList.add('buttons');
                    child_section.appendChild(button_section);

                    const open_button = document.createElement("button");
                    button_section.appendChild(open_button);
                    open_button.classList.add('project-button');
                    open_button.innerText = "Отвори";
                    open_button.value = item['project_id'];
                    open_button.onclick = function () {
                        reroute(this.value);
                    };

                    if (!item['not_owned']) {
                        const delete_button = document.createElement("button");
                        button_section.appendChild(delete_button);
                        delete_button.classList.add('delete-button');
                        delete_button.innerText = "Изтрий";
                        delete_button.value = item['project_id'];
                        delete_button.onclick = function () {
                            deleteProject(this.value);
                        };
                    }
                }
            } else {
                window.alert('Нямате създадени проекти!');
            }
        });
}


const reroute = (projectId) => {
    fetch('../php/my_projects.php', {
        method: 'POST',
        body: projectId
    })
        .then(r => {
            location.replace("./manual_estimation.html");
        });
}


const deleteProject = (projectId) => {
    fetch('../php/my_projects.php', {
        method: 'DELETE',
        body: projectId
    })
        .then(r => {
            window.alert('Проектът е изтрит.');
        })
        .then(r => {
            location.reload();
        });
}

document.addEventListener('DOMContentLoaded', loadData, false);
