<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include("includes/header.php");
?>

<main class="container py-4">
    <h2 class="mb-3">Welcome to ANSAR Hospital System</h2>
    <p class="text-muted">Select a module from the launcher to begin.</p>
</main>

<?php include("includes/footer.php"); ?>

<script>
// Submenu handling
const moduleSubmenus = <?= json_encode($modules) ?>;

document.querySelectorAll(".module-link").forEach(el => {
    el.addEventListener("click", e => {
        e.preventDefault();
        const selected = e.target.dataset.module;
        const submenuItems = moduleSubmenus[selected] || [];

        const subMenuContainer = document.getElementById("subMenuContainer");
        subMenuContainer.innerHTML = "";

        submenuItems.forEach(name => {
            const li = document.createElement("li");
            li.className = "nav-item";
            li.innerHTML = `<a href="#" class="nav-link text-white">${name}</a>`;
            subMenuContainer.appendChild(li);
        });
    });
});
</script>
