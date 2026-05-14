
document.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-carrito-accion");

    if (!btn) return;

    const formData = new FormData();
    formData.append("id", btn.dataset.id);
    formData.append("accion", btn.dataset.accion);

    fetch("/UNRINCONDEPT/public/ajax_operaciones_carrito.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            location.reload();
        } else {
            alert(data.message || "No se pudo actualizar el carrito.");
        }
    })
    .catch(error => {
        console.error("Error carrito:", error);
        alert("Error al actualizar el carrito.");
    });
});
