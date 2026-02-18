let cart = [];
let total = 0;

function addToCart(name, price) {
    cart.push({name, price});
    total += price;
    renderCart();
}

function renderCart() {
    let list = document.getElementById("cart-items");
    list.innerHTML = "";

    cart.forEach(item => {
        let li = document.createElement("li");
        li.textContent = item.name + " - " + item.price + " ₸";
        list.appendChild(li);
    });

    document.getElementById("total").textContent = "Итого: " + total + " ₸";
}

function openCart() {
    document.getElementById("cart").classList.add("active");
}

function closeCart() {
    document.getElementById("cart").classList.remove("active");
}

document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".product-card");

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, {
        threshold: 0.2
    });

    cards.forEach(card => {
        observer.observe(card);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const promoContent = document.querySelector(".promo-content");

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.3 });

    if (promoContent) {
        observer.observe(promoContent);
    }
});
