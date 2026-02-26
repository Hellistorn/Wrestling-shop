// Загружаем корзину из памяти браузера при старте
let cart = JSON.parse(localStorage.getItem('wrest_cart')) || [];
let total = cart.reduce((sum, item) => sum + item.price, 0);

// --- ЛОГИКА КОРЗИНЫ ---

function addToCart(name, price) {
    cart.push({name, price});
    total += price;
    saveCart(); 
    renderCart();
    console.log(`${name} добавлен в корзину`);
}

function renderCart() {
    let list = document.getElementById("cart-items");
    if (!list) return;
    list.innerHTML = "";

    cart.forEach((item, index) => {
        let li = document.createElement("li");
        li.innerHTML = `
            <span>${item.name}</span>
            <span style="margin-right: 15px; font-weight: bold;">${item.price.toLocaleString()} ₸</span>
            <button onclick="removeFromCart(${index})">✕</button>
        `;
        list.appendChild(li);
    });

    document.getElementById("total").innerHTML = `Итого: <span style="color: #e10600;">${total.toLocaleString()} ₸</span>`;
}

function removeFromCart(index) {
    total -= cart[index].price;
    cart.splice(index, 1);
    saveCart();
    renderCart();
}

function saveCart() {
    localStorage.setItem('wrest_cart', JSON.stringify(cart));
}

function openCart() {
    document.getElementById("cart").classList.add("active");
    renderCart();
}

function closeCart() {
    document.getElementById("cart").classList.remove("active");
}

// --- МОДАЛЬНОЕ ОКНО ОФОРМЛЕНИЯ ---

function openCheckoutForm() {
    if (cart.length === 0) return alert("Корзина пуста!");
    document.getElementById("checkout-modal").style.display = "block";
    document.getElementById("final-price").textContent = total.toLocaleString();
}

function closeCheckoutForm() {
    document.getElementById("checkout-modal").style.display = "none";
}

// --- ОТПРАВКА ЗАКАЗА В WHATSAPP ---

function sendOrder(event) {
    event.preventDefault(); // Останавливаем перезагрузку страницы

    const orderNumber = "№" + Math.floor(Math.random() * 90000 + 10000);
    
    const fio = document.getElementById("fio").value;
    const phone = document.getElementById("phone").value;
    const country = document.getElementById("country").value;
    const city = document.getElementById("city").value;
    const zip = document.getElementById("zip").value;
    const address = document.getElementById("address").value;

    let productList = "";
    cart.forEach((item, index) => {
        productList += `${index + 1}. ${item.name} — ${item.price.toLocaleString()} ₸\n`;
    });

    const message = `🛍️ *ЗАКАЗ ${orderNumber}* (WrestSpartan)\n\n` +
                    `👤 *КЛИЕНТ:* ${fio}\n` +
                    `📞 *ТЕЛ:* ${phone}\n` +
                    `--------------------------\n` +
                    `📍 *АДРЕС ДОСТАВКИ:*\n` +
                    `🌍 Страна: ${country}\n` +
                    `🏙️ Город: ${city}\n` +
                    `📮 Индекс: ${zip}\n` +
                    `🏠 Адрес: ${address}\n` +
                    `--------------------------\n` +
                    `📦 *ТОВАРЫ:*\n${productList}\n` +
                    `💰 *ИТОГО К ОПЛАТЕ:* ${total.toLocaleString()} ₸\n\n` +
                    `🚀 _Жду реквизиты для оплаты_`;

    const bossPhone = "87072745020"; 
    const url = `https://wa.me/${bossPhone}?text=${encodeURIComponent(message)}`;
    
    window.open(url, '_blank');
}

// --- АНИМАЦИИ И СКРОЛЛ ---

function revealProducts() {
    const cards = document.querySelectorAll('.product-card');
    cards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        const triggerPoint = window.innerHeight - 100;

        if (cardTop < triggerPoint) {
            setTimeout(() => {
                card.classList.add('show');
            }, index * 150); 
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    renderCart();

    // IntersectionObserver для карточек (дублирует логику revealProducts, но более современно)
    const cards = document.querySelectorAll(".product-card");
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));

    // Промо-блок
    const promoContent = document.querySelector(".promo-content");
    if (promoContent) {
        const promoObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                }
            });
        }, { threshold: 0.3 });
        promoObserver.observe(promoContent);
    }
});

// Слушатели событий
window.addEventListener('scroll', revealProducts);
window.addEventListener('load', revealProducts);

// Плавная прокрутка
document.querySelectorAll('a[href^="#"], a[href*="index.php#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href.includes('#')) {
            const targetId = href.split('#')[1];
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});