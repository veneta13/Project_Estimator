const loadData = () => {
    fetch('../php/my_projects.php',  {method:'GET'})
        .then(r=>r.json())
        .then(r=> {
            console.log("Fetch response:", r.result);
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

                    const button = document.createElement("button");
                    child_section.appendChild(button);
                    button.classList.add('project-button');
                    button.innerText = "Отвори";
                    button.value = item['project_id'];
                    button.onclick =  function() { reroute(this.value); };
                }
            } else {
                window.alert('Нямате създадени проекти!');
            }
        });
}


const reroute = (projectId) => {
    fetch('../php/my_projects.php',  {method:'POST', body:projectId})
        .then(r=>r.json())
        .then(r=> {
            location.replace("./manual_estimation.html");
        });
}

document.addEventListener('DOMContentLoaded', loadData, false);
