const loadData = () => {
    fetch('../php/profile.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                document.getElementById("username").innerHTML = r.result['name'];
                document.getElementById("tasks").innerHTML = r.result['task_count'];
                document.getElementById("time").innerHTML = r.result['task_time'];
                document.getElementById("invitations").innerHTML = r.result['invitations'];
            } else {
                window.alert('Грешка при вход, моля, проверете данните и опитайте отново!');
            }
        });

    fetch('../php/invitations.php', {method: 'GET'})
        .then(r => r.json())
        .then(r => {
            console.log("Fetch response:", r.result['invitations']);

            let item;
            const parent = document.getElementById("invitation_box");

            for (let index in r.result) {
                item = r.result[index];

                console.log(item);

                const child_section = document.createElement("section");
                parent.appendChild(child_section);
                child_section.classList.add('invitation');

                const project_name = document.createElement("h2");
                child_section.appendChild(project_name);
                project_name.innerText = item['name'];

                const project_owner = document.createElement("p");
                child_section.appendChild(project_owner);
                project_owner.classList.add('project-owner');
                project_owner.innerText = item['owner'];

                const button_section = document.createElement("section");
                button_section.classList.add('buttons');
                child_section.appendChild(button_section);

                const accept_button = document.createElement("button");
                button_section.appendChild(accept_button);
                accept_button.classList.add('accept-button');
                accept_button.innerText = "Приеми";
                accept_button.value = item['project_id'];
                accept_button.onclick = function () {
                    acceptInvitation(this.value);
                };

                const deny_button = document.createElement("button");
                button_section.appendChild(deny_button);
                deny_button.classList.add('deny-button');
                deny_button.innerText = "Откажи";
                deny_button.value = item['project_id'];
                deny_button.onclick = function () {
                    denyInvitation(this.value);
                };

            }
        });
}

const acceptInvitation = (projectId) => {
    fetch('../php/invitations.php', {
        method: 'PUT',
        body: projectId
    })
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                window.alert('Поканата е приета.');
            } else {
                window.alert('Възникна грешка.');
            }
        })
        .then(r =>
            location.reload()
        );
}

const denyInvitation = (projectId) => {
    fetch('../php/invitations.php', {
        method: 'DELETE',
        body: projectId
    })
        .then(r => r.json())
        .then(r => {
            if (r.result) {
                window.alert('Поканата е отхврърлена.');
            } else {
                window.alert('Възникна грешка.');
            }
        })
        .then(r =>
            location.reload()
        );
}

document.addEventListener('DOMContentLoaded', loadData, false);
