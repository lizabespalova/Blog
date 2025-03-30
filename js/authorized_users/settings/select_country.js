document.addEventListener("DOMContentLoaded", function () {
    let countrySelect = document.getElementById("country");
    let citySelect = document.getElementById("city");

    // Проверяем, что элементы существуют на странице
    if (!countrySelect || !citySelect) {
        console.error("Не найдены элементы с id 'country' или 'city'");
        return;
    }

    // При изменении страны загружаем города
    countrySelect.addEventListener("change", function () {
        let selectedCountry = this.value;
        citySelect.innerHTML = '<option value="">Loading cities...</option>';

        if (selectedCountry) {
            fetch(`https://countriesnow.space/api/v0.1/countries/cities/q?country=${encodeURIComponent(selectedCountry)}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value=""></option>';
                    if (data.error === false && data.data.length > 0) {
                        data.data.sort(); // Сортируем города по алфавиту
                        data.data.forEach(function(city) {
                            let option = document.createElement("option");
                            option.value = city;
                            option.textContent = city;
                            citySelect.appendChild(option);
                        });
                    } else {
                        citySelect.innerHTML = '<option value="">No cities found</option>';
                    }
                })
                .catch(error => {
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                    console.error("Error fetching cities:", error);
                });
        }
    });

    // При изменении города или страны сразу отправляем данные на сервер
    countrySelect.addEventListener("change", saveLocation);
    citySelect.addEventListener("change", saveLocation);

    function saveLocation() {
        let country = countrySelect.value;
        let city = citySelect.value;

        if (country && city) {
            fetch("/settings/change-location", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `country=${encodeURIComponent(country)}&city=${encodeURIComponent(city)}`
            })
                .then(response => response.json())
                .catch(error => {
                    console.error("Error saving location:", error);
                });
        } else {
            console.warn("Please select both a country and a city.");
        }
    }
});
