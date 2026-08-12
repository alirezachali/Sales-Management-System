function updateClock() {
    const now = new Date();
    document.getElementById("liveClock").innerHTML =
        now.toLocaleTimeString("fa-IR");
}

setInterval(updateClock, 1000);
updateClock();

const toggleButton = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

if (toggleButton && sidebar) {
    toggleButton.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
    });
}