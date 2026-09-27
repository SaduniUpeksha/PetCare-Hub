// =========================================
// PetCare Hub - FINAL FRONTEND INTEGRATION
// =========================================

const API = "../backend/api/";

let allPets = [];
let allProducts = [];

let currentPetPage = 1;
let currentProductPage = 1;

const PETS_PER_PAGE = 6;
const PRODUCTS_PER_PAGE = 8;


// =========================================
// PAGE START
// =========================================

document.addEventListener("DOMContentLoaded", function () {
    startApp();
});


async function startApp() {

    const page = window.location.pathname
        .split("/")
        .pop()
        .toLowerCase();


    // LOGIN / REGISTER
    if (
        page === "login.html" ||
        page === "register.html"
    ) {

        const session = await getSession();

        if (session.loggedIn) {

            if (session.role === "admin") {

                window.location.href =
                    "../backend/admin/dashboard.php";

            } else {

                window.location.href =
                    "home.html";
            }
        }

        return;
    }


    // ALL OTHER FRONTEND PAGES REQUIRE LOGIN

    const session = await getSession();

    if (!session.loggedIn) {

        window.location.href = "login.html";

        return;
    }


    // ADMIN USERS GO TO ADMIN DASHBOARD

    if (session.role === "admin") {

        window.location.href =
            "../backend/admin/dashboard.php";

        return;
    }


    // CREATE SAME NAVBAR EVERYWHERE

    createNavbar(session);


    // LOAD PAGE CONTENT

    await loadHomeData();
    await loadPets();
    await loadProducts();
    await loadDoctors();
    await loadAppointmentForm();

    setupContactForm();
    setupMobileNavbar();
}


// =========================================
// SESSION
// =========================================

async function getSession() {

    try {

        const response = await fetch(
            API + "session.php",
            {
                credentials: "include",
                cache: "no-store"
            }
        );

        if (!response.ok) {
            return {
                loggedIn: false
            };
        }

        return await response.json();

    } catch (error) {

        console.error(
            "Session error:",
            error
        );

        return {
            loggedIn: false
        };
    }
}


// =========================================
// NAVBAR
// =========================================

function createNavbar(session) {

    const nav =
        document.getElementById("mainNav");

    if (!nav) {

        console.error(
            "mainNav was not found on this page."
        );

        return;
    }


    nav.innerHTML = `

        <li class="nav-item">
            <a class="nav-link"
               href="home.html">
                Home
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="pet.html">
                Pets
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="products.html">
                Products
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="doctors.html">
                Doctors
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="appointment.html">
                Appointment
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link fw-bold"
               href="../backend/user/dashboard.php">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link"
               href="../backend/user/cart.php">
                Cart
                (<span id="navbarCartCount">
                    ${session.cartCount || 0}
                </span>)
            </a>
        </li>

        <li class="nav-item ms-3">

            <span class="nav-link fw-bold">
                👤 ${escapeHTML(session.username)}
            </span>

        </li>

        <li class="nav-item">

            <a
                class="btn btn-dark rounded-pill px-4"
                href="../backend/auth/logout.php">

                Logout

            </a>

        </li>

    `;
}


// =========================================
// HOME
// =========================================

async function loadHomeData() {

    const petGrid =
        document.getElementById(
            "featuredPetsGrid"
        );

    const productGrid =
        document.getElementById(
            "featuredProductsGrid"
        );


    if (!petGrid && !productGrid) {
        return;
    }


    try {

        const [
            petResponse,
            productResponse
        ] = await Promise.all([

            fetch(API + "pets.php"),

            fetch(API + "products.php")
        ]);


        const pets =
            await petResponse.json();

        const products =
            await productResponse.json();


        if (petGrid) {

            if (
                Array.isArray(pets) &&
                pets.length
            ) {

                petGrid.innerHTML =
                    pets
                        .slice(0, 4)
                        .map(pet =>
                            createPetCard(
                                pet,
                                true
                            )
                        )
                        .join("");

            } else {

                petGrid.innerHTML =
                    `
                    <p class="text-center">
                        No featured pets available.
                    </p>
                    `;
            }
        }


        if (productGrid) {

            if (
                Array.isArray(products) &&
                products.length
            ) {

                productGrid.innerHTML =
                    products
                        .slice(0, 4)
                        .map(product =>
                            createProductCard(
                                product,
                                true
                            )
                        )
                        .join("");

            } else {

                productGrid.innerHTML =
                    `
                    <p class="text-center">
                        No featured products available.
                    </p>
                    `;
            }
        }


        attachCardButtons();

    } catch (error) {

        console.error(
            "Home data error:",
            error
        );
    }
}


// =========================================
// PETS
// =========================================

async function loadPets() {

    const grid =
        document.getElementById("petGrid");

    if (!grid) {
        return;
    }


    try {

        const response =
            await fetch(
                API + "pets.php"
            );

        allPets =
            await response.json();


        if (!Array.isArray(allPets)) {
            allPets = [];
        }


        document.body.dataset.petFilter =
            "all";


        renderPets();


        document
            .querySelectorAll(
                "[data-pet-filter]"
            )
            .forEach(button => {

                button.addEventListener(
                    "click",
                    function () {

                        document
                            .querySelectorAll(
                                "[data-pet-filter]"
                            )
                            .forEach(btn =>
                                btn.classList
                                    .remove("active")
                            );

                        this.classList.add("active");

                        document.body.dataset.petFilter =
                            this.dataset.petFilter
                                .toLowerCase();

                        currentPetPage = 1;

                        renderPets();
                    }
                );

            });


        const search =
            document.getElementById(
                "petSearch"
            );


        if (search) {

            search.addEventListener(
                "input",
                function () {

                    currentPetPage = 1;

                    renderPets();
                }
            );
        }

    } catch (error) {

        console.error(
            "Pet loading error:",
            error
        );

        grid.innerHTML =
            `
            <div class="col-12 text-center">
                <p>Unable to load pets.</p>
            </div>
            `;
    }
}


// =========================================
// RENDER PETS
// =========================================

function renderPets() {

    const grid =
        document.getElementById("petGrid");

    if (!grid) return;


    const search =
        document.getElementById(
            "petSearch"
        );


    const searchValue =
        search
            ? search.value
                .trim()
                .toLowerCase()
            : "";


    const filter =
        document.body.dataset.petFilter ||
        "all";


    const standardTypes = [
        "dog",
        "cat",
        "bird",
        "fish",
        "rabbit"
    ];


    const filtered =
        allPets.filter(pet => {

            const type =
                String(
                    pet.type || ""
                ).toLowerCase();


            let filterMatch = true;


            if (filter !== "all") {

                if (filter === "others") {

                    filterMatch =
                        !standardTypes.includes(
                            type
                        );

                } else {

                    filterMatch =
                        type === filter;
                }
            }


            const searchableText = `
                ${pet.pet_name || ""}
                ${pet.type || ""}
                ${pet.breed || ""}
                ${pet.description || ""}
            `.toLowerCase();


            const searchMatch =
                searchableText.includes(
                    searchValue
                );


            return (
                filterMatch &&
                searchMatch
            );
        });


    const start =
        (currentPetPage - 1) *
        PETS_PER_PAGE;


    const pagePets =
        filtered.slice(
            start,
            start + PETS_PER_PAGE
        );


    if (!pagePets.length) {

        grid.innerHTML =
            `
            <div class="col-12 text-center">
                <p>No pets found.</p>
            </div>
            `;

    } else {

        grid.innerHTML =
            pagePets
                .map(pet =>
                    createPetCard(pet)
                )
                .join("");
    }


    renderPagination(
        filtered.length,
        PETS_PER_PAGE,
        currentPetPage,
        "petPagination",
        function (page) {

            currentPetPage = page;

            renderPets();
        }
    );


    attachCardButtons();
}


// =========================================
// PRODUCTS
// =========================================

async function loadProducts() {

    const grid =
        document.getElementById(
            "productGrid"
        );

    if (!grid) return;


    try {

        const response =
            await fetch(
                API + "products.php"
            );


        allProducts =
            await response.json();


        if (!Array.isArray(allProducts)) {
            allProducts = [];
        }


        renderProducts();


        const search =
            document.getElementById(
                "productSearch"
            );


        if (search) {

            search.addEventListener(
                "input",
                function () {

                    currentProductPage = 1;

                    renderProducts();
                }
            );
        }

    } catch (error) {

        console.error(
            "Products error:",
            error
        );

        grid.innerHTML =
            `
            <div class="col-12 text-center">
                <p>Unable to load products.</p>
            </div>
            `;
    }
}


// =========================================
// RENDER PRODUCTS
// =========================================

function renderProducts() {

    const grid =
        document.getElementById(
            "productGrid"
        );

    if (!grid) return;


    const search =
        document.getElementById(
            "productSearch"
        );


    const searchValue =
        search
            ? search.value
                .trim()
                .toLowerCase()
            : "";


    const filtered =
        allProducts.filter(product => {

            const text = `
                ${product.product_name || ""}
                ${product.category || ""}
            `.toLowerCase();


            return text.includes(
                searchValue
            );
        });


    const start =
        (currentProductPage - 1) *
        PRODUCTS_PER_PAGE;


    const pageProducts =
        filtered.slice(
            start,
            start + PRODUCTS_PER_PAGE
        );


    if (!pageProducts.length) {

        grid.innerHTML =
            `
            <div class="col-12 text-center">
                <p>No products found.</p>
            </div>
            `;

    } else {

        grid.innerHTML =
            pageProducts
                .map(product =>
                    createProductCard(product)
                )
                .join("");
    }


    renderPagination(
        filtered.length,
        PRODUCTS_PER_PAGE,
        currentProductPage,
        "productPagination",
        function (page) {

            currentProductPage = page;

            renderProducts();
        }
    );


    attachCardButtons();
}


// =========================================
// DOCTORS
// =========================================

async function loadDoctors() {

    const grid =
        document.getElementById("doctorGrid");

    if (!grid) return;

    try {

        const response =
            await fetch(API + "doctors.php", {
                credentials: "include",
                cache: "no-store"
            });

        if (!response.ok) {
            throw new Error(
                "Doctor API HTTP error: " + response.status
            );
        }

        const doctors =
            await response.json();

        if (
            !Array.isArray(doctors) ||
            doctors.length === 0
        ) {

            grid.innerHTML =
                `
                <div class="col-12 text-center">
                    <p>No doctors available.</p>
                </div>
                `;

            return;
        }

        grid.innerHTML =
            doctors.map(doctor => {

                const image =
                    doctor.image
                        ? `images/${doctor.image}`
                        : "images/user.png";

                return `

                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="card doctor-card shadow h-100">

                            <img
                                src="${escapeAttribute(image)}"
                                class="doctor-img"
                                alt="${escapeAttribute(doctor.name)}"
                                onerror="this.src='images/user.png'"
                            >

                            <div class="card-body text-center d-flex flex-column">

                                <h4>
                                    ${escapeHTML(doctor.name)}
                                </h4>

                                <p>
                                    ${escapeHTML(
                                        doctor.specialization || "-"
                                    )}
                                </p>

                                <p>
                                    <strong>Experience:</strong>
                                    ${escapeHTML(
                                        doctor.experience || "-"
                                    )}
                                </p>

                                <p>
                                    <strong>Available:</strong>
                                </p>

                                <p>
                                    ${escapeHTML(
                                        doctor.availability || "-"
                                    )}
                                </p>

                                <a
                                    href="appointment.html?doctor=${doctor.id}"
                                    class="btn btn-primary mt-auto">

                                    Book Appointment

                                </a>

                            </div>

                        </div>

                    </div>

                `;

            }).join("");

    } catch (error) {

        console.error(
            "Doctor loading error:",
            error
        );

        grid.innerHTML =
            `
            <div class="col-12 text-center">
                <p>Unable to load doctors.</p>
                <small class="text-danger">
                    ${escapeHTML(error.message)}
                </small>
            </div>
            `;
    }
}


// =========================================
// APPOINTMENT
// =========================================

async function loadAppointmentForm() {

    const form =
        document.getElementById("appointmentForm");

    if (!form) return;

    const doctorSelect =
        document.getElementById("doctorSelect");

    const petSelect =
        document.getElementById("petSelect");


    /* =========================================
       LOAD DOCTORS
    ========================================= */

    try {

        const doctorResponse =
            await fetch(
                API + "doctors.php",
                {
                    credentials: "include",
                    cache: "no-store"
                }
            );

        if (!doctorResponse.ok) {
            throw new Error(
                "Doctor API HTTP error: " +
                doctorResponse.status
            );
        }

        const doctors =
            await doctorResponse.json();

        if (doctorSelect) {

            doctorSelect.innerHTML =
                `
                <option value="">
                    Select Doctor
                </option>
                `;

            if (Array.isArray(doctors)) {

                doctors.forEach(doctor => {

                    const option =
                        document.createElement("option");

                    option.value =
                        doctor.id;

                    option.textContent =
                        doctor.name;

                    doctorSelect.appendChild(option);

                });

            }


            /* Select doctor from Doctors page */

            const params =
                new URLSearchParams(
                    window.location.search
                );

            const selectedDoctor =
                params.get("doctor");

            if (selectedDoctor) {

                doctorSelect.value =
                    selectedDoctor;
            }
        }

    } catch (error) {

        console.error(
            "Doctor loading error:",
            error
        );

        if (doctorSelect) {

            doctorSelect.innerHTML =
                `
                <option value="">
                    Unable to load doctors
                </option>
                `;
        }
    }


    /* =========================================
       LOAD USER'S PETS
    ========================================= */

    try {

        const petResponse =
            await fetch(
                "../backend/user/my_pets_api.php",
                {
                    credentials: "include",
                    cache: "no-store"
                }
            );

        if (!petResponse.ok) {
            throw new Error(
                "Pet API HTTP error: " +
                petResponse.status
            );
        }

        const pets =
            await petResponse.json();

        if (petSelect) {

            petSelect.innerHTML =
                `
                <option value="">
                    Select Your Pet
                </option>
                `;

            if (
                Array.isArray(pets) &&
                pets.length > 0
            ) {

                pets.forEach(pet => {

                    const option =
                        document.createElement("option");

                    option.value =
                        pet.id;

                    option.textContent =
                        `${pet.pet_name} (${pet.type})`;

                    petSelect.appendChild(option);

                });

            } else {

                const option =
                    document.createElement("option");

                option.disabled = true;

                option.textContent =
                    "No pets found - add a pet from Dashboard";

                petSelect.appendChild(option);
            }
        }

    } catch (error) {

        console.error(
            "Pet loading error:",
            error
        );

        if (petSelect) {

            petSelect.innerHTML =
                `
                <option value="">
                    Unable to load your pets
                </option>
                `;
        }
    }


    /* =========================================
       SUBMIT APPOINTMENT
    ========================================= */

    if (!form.dataset.listenerAdded) {

        form.dataset.listenerAdded = "true";

        form.addEventListener(
            "submit",
            async function (event) {

                event.preventDefault();

                const formData =
                    new FormData(form);

                try {

                    const response =
                        await fetch(
                            API + "appointment.php",
                            {
                                method: "POST",
                                body: formData,
                                credentials: "include"
                            }
                        );

                    if (!response.ok) {
                        throw new Error(
                            "Appointment API error: " +
                            response.status
                        );
                    }

                    const result =
                        await response.json();

                    alert(result.message);

                    if (result.success) {
                        form.reset();
                    }

                } catch (error) {

                    console.error(
                        "Appointment error:",
                        error
                    );

                    alert(
                        "Unable to book appointment."
                    );
                }
            }
        );
    }
}

// =========================================
// CONTACT
// =========================================

function setupContactForm() {

    const form =
        document.getElementById(
            "contactForm"
        );


    if (!form) return;


    if (
        form.dataset.listenerAdded
    ) {
        return;
    }


    form.dataset.listenerAdded = "true";


    form.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();


            const formData =
                new FormData(form);


            try {

                const response =
                    await fetch(
                        "../backend/contact.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );


                const result =
                    await response.text();


                alert(result);

                form.reset();


            } catch (error) {

                console.error(error);

                alert(
                    "Unable to send message."
                );
            }
        }
    );
}


// =========================================
// CART BUTTON
// =========================================

function attachCardButtons() {

    document
        .querySelectorAll(
            ".add-cart-btn"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                async function () {

                    try {

                        const response =
                            await fetch(
                                API + "cart.php",
                                {
                                    method: "POST",

                                    headers: {
                                        "Content-Type":
                                            "application/json"
                                    },

                                    credentials:
                                        "include",

                                    body: JSON.stringify({
                                        action: "add",

                                        product_id:
                                            button.dataset
                                                .productId
                                    })
                                }
                            );


                        const result =
                            await response.json();


                        alert(
                            result.message
                        );


                        if (
                            result.success
                        ) {

                            updateCartCount();
                        }


                    } catch (error) {

                        console.error(error);

                        alert(
                            "Unable to add product."
                        );
                    }
                }
            );
        });


    /*
     * DETAILS BUTTON
     */

    document
        .querySelectorAll(
            ".details-btn"
        )
        .forEach(button => {

            button.addEventListener(
                "click",
                function () {

                    const id =
                        Number(
                            button.dataset.id
                        );


                    const type =
                        button.dataset.type;


                    if (
                        type === "product"
                    ) {

                        const product =
                            allProducts.find(
                                item =>
                                    Number(
                                        item.id
                                    ) === id
                            );


                        if (product) {

                            alert(
                                `${product.product_name}\n\n` +
                                `Category: ${
                                    product.category || "-"
                                }\n` +
                                `Price: Rs. ${
                                    product.price
                                }\n` +
                                `Available: ${
                                    product.quantity
                                }`
                            );
                        }

                    } else {

                        const pet =
                            allPets.find(
                                item =>
                                    Number(
                                        item.id
                                    ) === id
                            );


                        if (pet) {

                            alert(
                                `${pet.pet_name}\n\n` +
                                `Type: ${
                                    pet.type || "-"
                                }\n` +
                                `Breed: ${
                                    pet.breed || "-"
                                }\n` +
                                `Gender: ${
                                    pet.gender || "-"
                                }\n` +
                                `Age: ${
                                    pet.age || "-"
                                }\n` +
                                `Price: Rs. ${
                                    pet.price
                                }`
                            );
                        }
                    }
                }
            );
        });
}


// =========================================
// CART COUNT
// =========================================

async function updateCartCount() {

    const session =
        await getSession();


    const count =
        document.getElementById(
            "navbarCartCount"
        );


    if (count) {

        count.textContent =
            session.cartCount || 0;
    }
}


// =========================================
// PAGINATION
// =========================================

function renderPagination(
    total,
    perPage,
    currentPage,
    elementId,
    callback
) {

    const container =
        document.getElementById(
            elementId
        );


    if (!container) return;


    const totalPages =
        Math.ceil(
            total / perPage
        );


    if (totalPages <= 1) {

        container.innerHTML = "";

        return;
    }


    let html = `

        <li class="page-item ${
            currentPage === 1
                ? "disabled"
                : ""
        }">

            <a
                class="page-link"
                href="#"
                data-page="${currentPage - 1}">

                «

            </a>

        </li>
    `;


    for (
        let page = 1;
        page <= totalPages;
        page++
    ) {

        html += `

            <li class="page-item ${
                page === currentPage
                    ? "active"
                    : ""
            }">

                <a
                    class="page-link"
                    href="#"
                    data-page="${page}">

                    ${page}

                </a>

            </li>

        `;
    }


    html += `

        <li class="page-item ${
            currentPage === totalPages
                ? "disabled"
                : ""
        }">

            <a
                class="page-link"
                href="#"
                data-page="${currentPage + 1}">

                »

            </a>

        </li>

    `;


    container.innerHTML =
        html;


    container
        .querySelectorAll(
            "[data-page]"
        )
        .forEach(link => {

            link.addEventListener(
                "click",
                function (event) {

                    event.preventDefault();


                    const page =
                        Number(
                            link.dataset.page
                        );


                    if (
                        page >= 1 &&
                        page <= totalPages
                    ) {

                        callback(page);
                    }
                }
            );
        });
}


// =========================================
// MOBILE NAVBAR
// =========================================

function setupMobileNavbar() {

    document
        .querySelectorAll(
            ".navbar .nav-link"
        )
        .forEach(link => {

            link.addEventListener(
                "click",
                function () {

                    const collapse =
                        document.querySelector(
                            ".navbar-collapse"
                        );


                    if (
                        collapse &&
                        collapse.classList.contains(
                            "show"
                        )
                    ) {

                        const instance =
                            bootstrap.Collapse
                                .getInstance(
                                    collapse
                                );


                        if (instance) {

                            instance.hide();
                        }
                    }
                }
            );
        });
}


// =========================================
// SECURITY
// =========================================

function escapeHTML(value) {

    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}


function escapeAttribute(value) {

    return escapeHTML(value);
}


// =========================================
// PET CARD
// =========================================

function createPetCard(
    pet,
    featured = false
) {

    if (featured) {

        return `

            <div class="col-md-4 col-lg-3">

                <div class="card pet-card h-100 shadow-sm">

                    <img
                        src="images/${escapeAttribute(
                            pet.image ||
                            "user.png"
                        )}"
                        class="card-img-top"
                        alt="${escapeAttribute(
                            pet.pet_name
                        )}"
                        onerror="this.src='images/user.png'"
                    >

                    <div class="card-body text-center">

                        <h5 class="card-title">

                            ${escapeHTML(
                                pet.pet_name
                            )}

                        </h5>

                        <p class="text-muted">

                            ${escapeHTML(
                                pet.breed ||
                                pet.type ||
                                ""
                            )}

                        </p>

                        <p class="text-muted">

                            Rs.
                            ${Number(
                                pet.price
                            ).toLocaleString()}

                        </p>

                    </div>

                </div>

            </div>

        `;
    }


    return `

        <div class="col-lg-4 col-md-6">

            <div class="card shadow h-100">

                <img
                    src="images/${escapeAttribute(
                        pet.image ||
                        "user.png"
                    )}"
                    class="card-img-top"
                    alt="${escapeAttribute(
                        pet.pet_name
                    )}"
                    onerror="this.src='images/user.png'"
                >

                <div class="card-body text-center">

                    <h5>
                        ${escapeHTML(
                            pet.pet_name
                        )}
                    </h5>

                    <p>

                        <strong>
                            Breed:
                        </strong>

                        ${escapeHTML(
                            pet.breed || "-"
                        )}

                    </p>

                    <p>

                        <strong>
                            Gender:
                        </strong>

                        ${escapeHTML(
                            pet.gender || "-"
                        )}

                    </p>

                    <p>

                        <strong>
                            Age:
                        </strong>

                        ${pet.age ?? "-"}

                    </p>

                    <p>

                        <strong>
                            Price:
                        </strong>

                        Rs.

                        ${Number(
                            pet.price
                        ).toLocaleString()}

                    </p>

                    <button
                        class="btn btn-primary btn-sm details-btn"
                        data-type="pet"
                        data-id="${pet.id}">

                        View Details

                    </button>

                </div>

            </div>

        </div>

    `;
}


// =========================================
// PRODUCT CARD
// =========================================

function createProductCard(
    product,
    featured = false
) {

    return `

        <div class="${
            featured
                ? "col-md-6 col-lg-3"
                : "col-lg-3 col-md-6 mb-4"
        }">

            <div class="card shadow h-100">

                <img
                    src="images/${escapeAttribute(
                        product.image ||
                        "user.png"
                    )}"
                    class="card-img-top"
                    alt="${escapeAttribute(
                        product.product_name
                    )}"
                    onerror="this.src='images/user.png'"
                >

                <div class="card-body text-center">

                    <h5>

                        ${escapeHTML(
                            product.product_name
                        )}

                    </h5>

                    <p class="text-muted">

                        ${escapeHTML(
                            product.category ||
                            "Pet Care Product"
                        )}

                    </p>

                    <h6 class="text-primary fw-bold mb-3">

                        Rs.

                        ${Number(
                            product.price
                        ).toLocaleString()}

                    </h6>

                    <button
                        class="btn btn-success btn-sm add-cart-btn"
                        data-product-id="${product.id}">

                        + Add to Cart

                    </button>

                </div>

            </div>

        </div>

    `;
}