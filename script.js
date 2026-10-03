document.addEventListener("DOMContentLoaded", function () {

    var box = document.getElementById("description");
    var counter = document.getElementById("descCount");

    if (box && counter) {
        var updateCounter = function () {
            var length = box.value.length;
            counter.textContent = length;

            if (length >= box.maxLength - 50) {
                counter.parentElement.classList.add("text-danger");
            } else {
                counter.parentElement.classList.remove("text-danger");
            }
        };

        box.addEventListener("input", updateCounter);
        updateCounter();
    }

    var alerts = document.querySelectorAll(".alert-auto-dismiss");

    alerts.forEach(function (alertBox) {
        setTimeout(function () {
            bootstrap.Alert.getOrCreateInstance(alertBox).close();
        }, 4000);
    });

});
