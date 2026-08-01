// =========================================
// PetCare Hub - Main JavaScript File
// =========================================

// ---------- Mobile Navbar ----------
document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".navbar .nav-link");

    navLinks.forEach(link => {
        link.addEventListener("click", () => {
            const navbarCollapse = document.querySelector(".navbar-collapse");
            if (navbarCollapse.classList.contains("show")) {
                bootstrap.Collapse.getInstance(navbarCollapse).hide();
            }
        });
    });
});

// ---------- Smooth Scrolling ----------
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));

        if (target) {
            target.scrollIntoView({
                behavior: "smooth"
            });
        }
    });
});

// ---------- Search Filter (Pets & Products) ----------
const searchInput = document.querySelector("input[placeholder*='Search']");

if (searchInput) {
    searchInput.addEventListener("keyup", function () {
        const value = this.value.toLowerCase();
        const cards = document.querySelectorAll(".card");

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();

            if (text.includes(value)) {
                card.parentElement.style.display = "block";
            } else {
                card.parentElement.style.display = "none";
            }
        });
    });
}

// ---------- Add To Cart ----------
const addButtons = document.querySelectorAll(".btn-success");

addButtons.forEach(button => {
    if (button.innerText.includes("Add")) {
        button.addEventListener("click", function () {
            alert("Product added to cart!");
        });
    }
});

// ---------- Appointment Form ----------
const appointmentForm = document.getElementById("appointmentForm");

if (appointmentForm) {
    appointmentForm.addEventListener("submit", function (e) {
        e.preventDefault();

        alert("Appointment booked successfully!");

        this.reset();
    });
}

// ---------- Contact Form ----------
const contactForm = document.getElementById("contactForm");

if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
        e.preventDefault();

        alert("Thank you! Your message has been sent.");

        this.reset();
    });
}

// ---------- Buy Pet ----------
document.querySelectorAll(".btn-primary, .btn-success").forEach(btn => {
    if (btn.innerText.includes("Buy")) {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            alert("Thank you for choosing this pet!");
        });
    }
});

// ---------- Scroll Animation ----------
const cards = document.querySelectorAll(".card");

window.addEventListener("scroll", () => {
    cards.forEach(card => {
        const cardTop = card.getBoundingClientRect().top;

        if (cardTop < window.innerHeight - 50) {
            card.classList.add("show-card");
        }
    });
});