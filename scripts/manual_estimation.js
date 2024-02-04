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

document.addEventListener('DOMContentLoaded', loadData, false);
