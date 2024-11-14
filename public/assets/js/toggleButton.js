document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        // Check if the clicked element is a toggle button or contains it
        const button = event.target.closest(".toggle-active-btn");
        if (!button) return;

        const modelId = button.getAttribute("data-id");
        const model = button.getAttribute("data-model");

        fetch(`/admin/${model}/toggle-active`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
            body: JSON.stringify({ id: modelId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    // Toggle button color based on activation status
                    button.classList.toggle("btn-success", data.is_active);
                    button.classList.toggle("btn-danger", !data.is_active);
                    button.style.transition = "all 0.3s ease-out";

                    // Update the icon inside the button based on the active status
                    const icon = button.querySelector("i");
                    if (icon) {
                        icon.classList.toggle("la-toggle-off", !data.is_active);
                        icon.classList.toggle("la-toggle-on", data.is_active);
                    }

                    // Update the "is_active" text in the status column
                    const statusCell = button
                        .closest("tr")
                        .querySelector(".status-cell");
                    if (statusCell) {
                        statusCell.textContent = data.is_active ? "نعم" : "لا";
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});
