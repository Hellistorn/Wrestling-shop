// Загружаем корзину из памяти браузера при старте
let cart = JSON.parse(localStorage.getItem('wrest_cart')) || [];
let total = cart.reduce((sum, item) => sum + item.price, 0);

// --- ЛОГИКА КОРЗИНЫ ---

function addToCart(name, price) {
    cart.push({name, price});
    total += price;
    saveCart(); // Сохраняем изменения
    renderCart();
    
    // Эффект нажатия (опционально)
    console.log(`${name} добавлен в корзину`);
}

function renderCart() {
    let list = document.getElementById("cart-items");
    list.innerHTML = "";

    cart.forEach((item, index) => {
        let li = document.createElement("li");
        
        // Используем HTML для структуры внутри li
        li.innerHTML = `
            <span>${item.name}</span>
            <span style="margin-right: 15px; font-weight: bold;">${item.price.toLocaleString()} ₸</span>
            <button onclick="removeFromCart(${index})">✕</button>
        `;
        list.appendChild(li);
    });

    // Выводим итоговую сумму. toLocaleString() добавит пробелы (18 000 вместо 18000)
    document.getElementById("total").innerHTML = `Итого: <span style="color: #e10600;">${total.toLocaleString()} ₸</span>`;
}

function removeFromCart(index) {
    total -= cart[index].price;
    cart.splice(index, 1);
    saveCart();
    renderCart();
}

function saveCart() {
    // Записываем массив в localStorage
    localStorage.setItem('wrest_cart', JSON.stringify(cart));
}

function openCart() {
    document.getElementById("cart").classList.add("active");
    renderCart(); // Перерисовываем при открытии
}

function closeCart() {
    document.getElementById("cart").classList.remove("active");
}

// Функция для оформления через WhatsApp
function checkout() {
    if (cart.length === 0) return alert("Корзина пуста");
    
    let message = "Здравствуйте! Я хочу заказать:\n";
    cart.forEach(item => {
        message += `- ${item.name} (${item.price} ₸)\n`;
    });
    message += `\nИтого: ${total} ₸`;
    
    const phone = "77777777777"; // Вставь сюда свой номер
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
}

// --- АНИМАЦИИ И СКРОЛЛ (ТВОЯ СХЕМА) ---

document.addEventListener("DOMContentLoaded", function () {
    renderCart(); // Инициализация корзины при загрузке

    // Анимация появления карточек товаров
    const cards = document.querySelectorAll(".product-card");
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.2 });

    cards.forEach(card => observer.observe(card));

    // Анимация промо-блока
    const promoContent = document.querySelector(".promo-content");
    const promoObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.3 });

    if (promoContent) promoObserver.observe(promoContent);
});

// Плавная прокрутка для ссылок-якорей
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

function openCheckoutForm() {
    if (cart.length === 0) return alert("Корзина пуста!");
    document.getElementById("checkout-modal").style.display = "block";
    document.getElementById("final-price").textContent = total.toLocaleString();
}

function closeCheckoutForm() {
    document.getElementById("checkout-modal").style.display = "none";
}

function sendOrder(event) {
    event.preventDefault();

    // Генерируем случайный номер заказа для сверки
    const orderNumber = "№" + Math.floor(Math.random() * 90000 + 10000);
    
    const fio = document.getElementById("fio").value;
    const phone = document.getElementById("phone").value;
    // ... (остальные поля как раньше)

    let productList = "";
    cart.forEach((item, index) => {
        productList += `${index + 1}. ${item.name} — ${item.price} ₸\n`;
    });

    // Формируем текст сообщения
    const message = `🛍️ ЗАКАЗ ${orderNumber}\n\n` +
                    `👤 Клиент: ${fio}\n` +
                    `📞 Тел: ${phone}\n` +
                    `📦 ТОВАРЫ:\n${productList}\n` +
                    `💰 ИТОГО К ОПЛАТЕ: ${total} ₸\n\n`;

    const bossPhone = "87072745020"; // Номер босса для получения заказа
    const url = `https://wa.me/${bossPhone}?text=${encodeURIComponent(message)}`;
    
    // Очистка и переход
    window.open(url, '_blank');
}

// Функция запуска анимации при прокрутке до секции
function revealProducts() {
    const cards = document.querySelectorAll('.product-card');
    cards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        const triggerPoint = window.innerHeight - 100;

        if (cardTop < triggerPoint) {
            // Добавляем задержку для каждой следующей карточки (эффект лесенки)
            setTimeout(() => {
                card.classList.add('show');
            }, index * 150); 
        }
    });
}

// Запускаем при скролле и при загрузке страницы
window.addEventListener('scroll', revealProducts);
window.addEventListener('load', revealProducts);