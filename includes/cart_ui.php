<div id="cart" class="side-cart">
    <div class="cart-header">
        <h2>Ваша корзина</h2>
        <button class="close-cart-btn" onclick="closeCart()">✕</button>
    </div>
    <ul id="cart-items" class="cart-list"></ul>
    <div class="cart-footer">
        <div id="total">Итого: 0 ₸</div>
        <button onclick="openCheckoutForm()" class="checkout-btn">Перейти к оформлению</button>
        <button onclick="closeCart()" class="continue-btn">Продолжить покупки</button>
    </div>
</div>

<div id="checkout-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeCheckoutForm()">&times;</span>
        <h2>Оформление заказа</h2>
        <form id="order-form" onsubmit="sendOrder(event)">
            <input type="text" id="fio" placeholder="ФИО полностью" required>
            <input type="tel" id="phone" placeholder="Номер телефона" required>

            <select id="country" required style="width: 100%; padding: 12px; margin-bottom: 10px; border: 1px solid #333; background: #111; color: #fff; border-radius: 5px;">
                <option value="" disabled selected>Выберите страну доставки</option>
                <option value="Казахстан">🇰🇿 Казахстан</option>
                <option value="Россия">🇷🇺 Россия</option>
                <option value="Кыргызстан">🇰🇬 Кыргызстан</option>
                <option value="Узбекистан">🇺🇿 Узбекистан</option>
            </select>

            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" id="city" placeholder="Город" required style="flex: 2;">
                <input type="text" id="zip" placeholder="Индекс" required style="flex: 1;">
            </div>

            <input type="text" id="address" placeholder="Адрес (улица, дом, квартира)" required>
            
            <div class="order-summary" style="background: #222; padding: 15px; border-radius: 10px; margin: 15px 0;">
                <p>К оплате: <span id="final-price" style="color: #e10600; font-size: 1.5rem; font-weight: bold;">0</span> ₸</p>
            </div>
            
            <button type="submit" class="confirm-btn" style="margin-top: 20px; background: #25d366;">Подтвердить заказ</button>
        </form>
    </div>
</div>