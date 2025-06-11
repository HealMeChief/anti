// Корзина: хранение и инициализация
let cart = [];

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    if (window.location.pathname.includes('corzina.php')) {
        loadCart();
    }
    updateCartCounter();
});

// Настройка событий
function setupEventListeners() {
    // Меню
    const menuButton = document.querySelector('.image-button');
    const navLinks = document.querySelector('.nav-links');
    const menuImage = menuButton?.querySelector('img');

    if (menuButton && navLinks && menuImage) {
        menuButton.addEventListener('click', (event) => {
            event.stopPropagation();
            navLinks.classList.toggle('open');
            menuImage.src = navLinks.classList.contains('open') ? 'close-button.png' : 'menu.png';
        });

        document.addEventListener('click', (event) => {
            if (!navLinks.contains(event.target)) {
                navLinks.classList.remove('open');
                menuImage.src = 'menu.png';
            }
        });

        navLinks.addEventListener('click', (event) => {
            event.stopPropagation();
        });
    }

    // Делегирование кликов по кнопкам "В корзину"
    document.body.addEventListener('click', (e) => {
        if (e.target.matches('.buy-button button')) {
            const itemElement = e.target.closest('.item');
            if (itemElement) {
                addToCart(itemElement);
            }
        }
    });
}

// Загрузка корзины с сервера
async function loadCart() {
    const cartContainer = document.getElementById('cart-container');
    if (!cartContainer) return;

    try {
        const response = await fetch('includes/cart-api.php?action=get');
        const { items } = await response.json();

        cart = items || [];

        renderCart();
    } catch (error) {
        console.error('Ошибка загрузки корзины:', error);
        cartContainer.innerHTML = `<div style="color: red;">Ошибка загрузки корзины. Пожалуйста, обновите страницу.</div>`;
    }
}

// Добавление товара
async function addToCart(itemElement) {
    const productId = itemElement.dataset.id;
    try {
        const response = await fetch(`includes/cart-api.php?action=add&product_id=${productId}`);
        const result = await response.json();

        if (result.success) {
            showNotification('Товар добавлен в корзину!');
            if (window.location.pathname.includes('corzina.php')) {
                await loadCart();
            }
            updateCartCounter();
        } else {
            showNotification('Ошибка: ' + (result.error || 'Не удалось добавить товар'), true);
        }
    } catch (error) {
        console.error('Ошибка:', error);
        showNotification('Ошибка соединения с сервером', true);
    }
}

// Удаление товара
async function removeFromCart(productId) {
    try {
        const response = await fetch(`includes/cart-api.php?action=remove&product_id=${productId}`);
        const result = await response.json();

        if (result.success) {
            cart = cart.filter(item => item.product_id !== productId);
            renderCart();
            updateCartCounter();
        }
    } catch (error) {
        console.error('Ошибка удаления из корзины:', error);
    }
}

// Изменение количества товара
async function changeQuantity(productId, delta) {
    const item = cart.find(item => item.product_id === productId);
    if (!item) return;

    const newQuantity = item.quantity + delta;
    if (newQuantity <= 0) {
        await removeFromCart(productId);
        return;
    }

    try {
        const response = await fetch('includes/cart-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'update', product_id: productId, quantity: newQuantity })
        });
        const result = await response.json();

        if (result.success) {
            item.quantity = newQuantity;
            renderCart();
            updateCartCounter();
        }
    } catch (error) {
        console.error('Ошибка изменения количества:', error);
    }
}

// Оформление заказа
async function placeOrder() {
    if (cart.length === 0) return;

    const name = prompt('Введите ваше имя:');
    const phone = prompt('Введите ваш телефон:');
    const address = prompt('Введите адрес доставки:');

    if (!name || !phone || !address) {
        alert('Все поля обязательны!');
        return;
    }

    try {
        const response = await fetch('includes/order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: cart, customer_name: name, customer_phone: phone, shipping_address: address })
        });

        const result = await response.json();

        if (result.success) {
            cart = [];
            renderCart();
            updateCartCounter();
            showNotification('Заказ оформлен! Номер заказа: ' + result.order_id);
        } else {
            alert('Ошибка оформления заказа: ' + (result.error || 'Неизвестная ошибка'));
        }
    } catch (error) {
        console.error('Ошибка заказа:', error);
        alert('Ошибка при оформлении заказа. Попробуйте позже.');
    }
}

// Отображение корзины
function renderCart() {
    const cartContainer = document.getElementById('cart-container');
    if (!cartContainer) return;

    cartContainer.innerHTML = '';

    if (cart.length === 0) {
        cartContainer.innerHTML = `<div id="empty-message" style="text-align:center;font-size:18px;color:grey;margin-top:20px;">Ваша корзина пуста.</div>`;
    } else {
        cart.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'k-item';
            itemElement.dataset.id = item.product_id;
            itemElement.innerHTML = `
                <div class="k-img">
                    <img src="${item.image_url}" alt="${item.name}">
                </div>
                <div class="k-text">
                    <p>${item.name}</p>
                    <p>${item.price} ₽</p>
                </div>
                <div class="k-controls">
                    <button class="k-minus" onclick="changeQuantity(${item.product_id}, -1)">-</button>
                    <span class="k-quantity">${item.quantity}</span>
                    <button class="k-plus" onclick="changeQuantity(${item.product_id}, 1)">+</button>
                    <button class="k-remove" onclick="removeFromCart(${item.product_id})">Удалить</button>
                </div>
            `;
            cartContainer.appendChild(itemElement);
        });
    }

    updateOrderButtonState();
}

function updateOrderButtonState() {
    const orderButton = document.getElementById('order-button');
    if (orderButton) {
        orderButton.disabled = cart.length === 0;
    }
}

function showNotification(message, isError = false) {
    const note = document.createElement('div');
    note.textContent = message;
    Object.assign(note.style, {
        position: 'fixed', bottom: '20px', right: '20px', padding: '10px 20px', borderRadius: '5px',
        backgroundColor: isError ? '#f44336' : '#4CAF50', color: 'white', zIndex: 1000
    });
    document.body.appendChild(note);
    setTimeout(() => {
        note.style.opacity = '0';
        setTimeout(() => note.remove(), 500);
    }, 3000);
}

async function updateCartCounter() {
    try {
        const response = await fetch('includes/cart-api.php?action=get');
        const { items } = await response.json();
        const count = items ? items.reduce((sum, item) => sum + item.quantity, 0) : 0;

        let counter = document.getElementById('cart-counter');
        if (!counter) {
            counter = document.createElement('span');
            counter.id = 'cart-counter';
            document.querySelector('.nav-bar-r')?.appendChild(counter);
        }
        counter.textContent = count > 0 ? ` (${count})` : '';
    } catch (err) {
        console.error('Ошибка обновления счётчика корзины:', err);
    }
}

// Глобальный доступ к функциям
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.changeQuantity = changeQuantity;
window.placeOrder = placeOrder;
window.renderCart = renderCart;
